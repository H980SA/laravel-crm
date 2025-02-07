<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\DataGrids\Lead\LeadDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\LeadForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Admin\Http\Resources\LeadResource;
use Webkul\Admin\Http\Resources\StageResource;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\DataGrid\Enums\DateRangeOptionEnum;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\ProductRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\StageRepository;
use Webkul\Lead\Repositories\TypeRepository;
use Webkul\Tag\Repositories\TagRepository;
use Webkul\User\Repositories\UserRepository;

class LeadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected AttributeRepository $attributeRepository,
        protected SourceRepository $sourceRepository,
        protected TypeRepository $typeRepository,
        protected PipelineRepository $pipelineRepository,
        protected StageRepository $stageRepository,
        protected LeadRepository $leadRepository,
        protected ProductRepository $productRepository,
    ) {
        request()->request->add(['entity_type' => 'leads']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(LeadDataGrid::class)->process();
        }

        if (request('pipeline_id')) {
            $pipeline = $this->pipelineRepository->find(request('pipeline_id'));
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();
        }

        return view('admin::leads.index', [
            'pipeline' => $pipeline,
            'columns'  => $this->getKanbanColumns(),
        ]);
    }

    /**
     * Returns a listing of the resource.
     */
    public function get(): JsonResponse
    {
        try {
            if (request()->query('pipeline_id')) {
                $pipeline = $this->pipelineRepository->find(request()->query('pipeline_id'));
            } else {
                $pipeline = $this->pipelineRepository->getDefaultPipeline();
            }

            if (!$pipeline) {
                \Log::error('No pipeline found');
                return response()->json(['error' => 'No pipeline found'], 500);
            }

            if ($stageId = request()->query('pipeline_stage_id')) {
                $stages = $pipeline->stages->where('id', request()->query('pipeline_stage_id'));
            } else {
                $stages = $pipeline->stages;
            }

            if ($stages->isEmpty()) {
                \Log::error('No stages found for pipeline: ' . $pipeline->id);
                return response()->json(['error' => 'No stages found'], 500);
            }

            $data = [];

            foreach ($stages as $stage) {
                try {
                    $query = app(LeadRepository::class)
                        ->pushCriteria(app(RequestCriteria::class))
                        ->where([
                            'lead_pipeline_id'       => $pipeline->id,
                            'lead_pipeline_stage_id' => $stage->id,
                        ]);

                    if ($userIds = bouncer()->getAuthorizedUserIds()) {
                        $query->whereIn('leads.user_id', $userIds);
                    }

                    $stage->lead_value = (clone $query)->sum('lead_value');

                    $data[$stage->sort_order] = (new StageResource($stage))->jsonSerialize();

                    // Get leads with eager loading
                    $leads = $query->with([
                        'tags',
                        'type',
                        'source',
                        'user',
                        'person',
                        'person.organization',
                        'pipeline',
                        'pipeline.stages',
                        'stage',
                        'attribute_values',
                        'attribute_values.attribute',
                        'attribute_values.attribute.options',
                    ])->get();

                    // Procesar los valores de los atributos para cada lead
                    foreach ($leads as $lead) {
                        $processedAttributes = [];
                        foreach ($lead->attribute_values as $attributeValue) {
                            $attribute = $attributeValue->attribute;
                            if (!$attribute) continue;

                            $value = null;
                            switch ($attribute->type) {
                                case 'text':
                                    $value = $attributeValue->text_value;
                                    break;
                                case 'boolean':
                                    $value = $attributeValue->boolean_value;
                                    break;
                                case 'integer':
                                    $value = $attributeValue->integer_value;
                                    break;
                                case 'float':
                                    $value = $attributeValue->float_value;
                                    break;
                                case 'datetime':
                                    $value = $attributeValue->datetime_value;
                                    break;
                                case 'date':
                                    $value = $attributeValue->date_value;
                                    break;
                                case 'select':
                                    if ($option = $attribute->options->where('id', $attributeValue->integer_value)->first()) {
                                        $value = $option->name;
                                    }
                                    break;
                                case 'lookup':
                                    // Para el caso del ejecutivo comercial (lookup)
                                    if ($attributeValue->text_value) {
                                        $value = $attributeValue->text_value;
                                    }
                                    break;
                            }
                            
                            if ($value !== null) {
                                $processedAttributes[$attribute->code] = $value;
                            }
                        }
                        
                        // Log para debug
                        \Log::info('Processed attributes for lead ' . $lead->id, $processedAttributes);
                        
                        $lead->processed_attributes = $processedAttributes;
                    }

                    // Manually load persons relationship with organization
                    foreach ($leads as $lead) {
                        $persons = DB::table('lead_persons')
                            ->join('persons', 'lead_persons.person_id', '=', 'persons.id')
                            ->leftJoin('organizations', 'persons.organization_id', '=', 'organizations.id')
                            ->where('lead_persons.lead_id', $lead->id)
                            ->select(
                                'persons.*',
                                'organizations.id as organization_id',
                                'organizations.name as organization_name'
                            )
                            ->get()
                            ->map(function($person) {
                                if ($person->organization_id) {
                                    $person->organization = (object)[
                                        'id' => $person->organization_id,
                                        'name' => $person->organization_name
                                    ];
                                } else {
                                    $person->organization = null;
                                }
                                unset($person->organization_id);
                                unset($person->organization_name);
                                return $person;
                            });
                        
                        $lead->setRelation('persons', $persons);
                    }

                    // Paginate the collection manually
                    $page = request()->input('page', 1);
                    $perPage = 10;
                    $items = $leads->forPage($page, $perPage);
                    
                    $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                        $items,
                        $leads->count(),
                        $perPage,
                        $page
                    );

                    $data[$stage->sort_order]['leads'] = [
                        'data' => LeadResource::collection($paginator)->additional(['processed_attributes' => true]),
                        'meta' => [
                            'current_page' => $paginator->currentPage(),
                            'from'         => $paginator->firstItem(),
                            'last_page'    => $paginator->lastPage(),
                            'per_page'     => $paginator->perPage(),
                            'to'           => $paginator->lastItem(),
                            'total'        => $paginator->total(),
                        ],
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error processing stage ' . $stage->id . ': ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return response()->json($data);
            
        } catch (\Exception $e) {
            \Log::error('Error in LeadController@get: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::leads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadForm $request): RedirectResponse
    {
        Event::dispatch('lead.create.before');

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Asegurarse que persons sea un array válido y removerlo de los datos principales
            $persons = [];
            if (!empty($data['persons']) && is_array($data['persons'])) {
                $persons = array_filter($data['persons']); // Eliminar valores vacíos
            }
            unset($data['persons']);

            // Asignar el usuario actual como propietario del lead
            $data['user_id'] = auth()->id();
            $data['status'] = 1;

            if (request()->input('lead_pipeline_stage_id')) {
                $stage = $this->stageRepository->findOrFail($data['lead_pipeline_stage_id']);
                $data['lead_pipeline_id'] = $stage->lead_pipeline_id;
            } else {
                $pipeline = $this->pipelineRepository->getDefaultPipeline();
                $stage = $pipeline->stages()->first();
                $data['lead_pipeline_id'] = $pipeline->id;
                $data['lead_pipeline_stage_id'] = $stage->id;
            }

            if (in_array($stage->code, ['won', 'lost'])) {
                $data['closed_at'] = Carbon::now();
            }

            // Crear el lead
            $lead = $this->leadRepository->create($data);

            // Adjuntar las personas seleccionadas
            if (!empty($persons)) {
                foreach ($persons as $personId) {
                    if (!empty($personId)) {
                        $lead->persons()->attach($personId);
                    }
                }
            }

            // Crear las tareas del Gantt
            $this->createGanttTasks($lead, $data);

            DB::commit();

            Event::dispatch('lead.create.after', $lead);

            session()->flash('success', trans('admin::app.leads.create-success'));

            return redirect()->route('admin.leads.index', $data['lead_pipeline_id']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear lead:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? null
            ]);

            session()->flash('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Crear las tareas del Gantt para un lead
     */
    private function createGanttTasks($lead, $data)
    {
        try {
            // Obtener las fechas de los atributos del lead o usar valores por defecto
            $startDate = isset($data['fecha_inicio_licitacion']) 
                ? Carbon::parse($data['fecha_inicio_licitacion'])
                : Carbon::now();
            
            $endDate = isset($data['fecha_cierre_licitacion'])
                ? Carbon::parse($data['fecha_cierre_licitacion'])
                : $startDate->copy()->addDays(30);

            // Calcular la duración en días
            $duration = $startDate->diffInDays($endDate) + 1;

            // Crear la tarea principal (licitación)
            $mainTask = \App\Models\Gannt::create([
                'text' => $lead->title,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'duration' => $duration,
                'progress' => 0,
                'priority' => 'Media',
                'is_parent' => true,
                'lead_id' => $lead->id
            ]);

            \Log::info('Tarea principal creada:', $mainTask->toArray());

            // Crear las subtareas estándar
            $subtasks = [
                [
                    'text' => 'Inicio de Proyecto - ' . $lead->title,
                    'duration' => 5,
                    'priority' => 'Alta'
                ],
                [
                    'text' => 'Planificación - ' . $lead->title,
                    'duration' => 7,
                    'priority' => 'Alta'
                ],
                [
                    'text' => 'Ejecución - ' . $lead->title,
                    'duration' => 10,
                    'priority' => 'Media'
                ],
                [
                    'text' => 'Control y Seguimiento - ' . $lead->title,
                    'duration' => 5,
                    'priority' => 'Media'
                ],
                [
                    'text' => 'Cierre - ' . $lead->title,
                    'duration' => 3,
                    'priority' => 'Alta'
                ]
            ];

            $currentStartDate = $startDate->copy();

            foreach ($subtasks as $subtask) {
                $subtaskEndDate = $currentStartDate->copy()->addDays($subtask['duration'] - 1);

                $task = \App\Models\Gannt::create([
                    'text' => $subtask['text'],
                    'start_date' => $currentStartDate->format('Y-m-d'),
                    'end_date' => $subtaskEndDate->format('Y-m-d'),
                    'duration' => $subtask['duration'],
                    'progress' => 0,
                    'priority' => $subtask['priority'],
                    'is_parent' => false,
                    'parent_id' => $mainTask->id,
                    'lead_id' => $lead->id
                ]);

                \Log::info('Subtarea creada:', $task->toArray());

                $currentStartDate = $subtaskEndDate->copy()->addDay();
            }
        } catch (\Exception $e) {
            \Log::error('Error al crear tareas del Gantt:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $lead = $this->leadRepository->findOrFail($id);

        return view('admin::leads.edit', compact('lead'));
    }

    /**
     * Display a resource.
     */
    public function view(int $id): View
    {
        $lead = $this->leadRepository->findOrFail($id);

        if (
            $userIds = bouncer()->getAuthorizedUserIds()
            && ! in_array($lead->user_id, $userIds)
        ) {
            return redirect()->route('admin.leads.index');
        }

        return view('admin::leads.view', compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadForm $request, int $id): RedirectResponse|JsonResponse
    {
        Event::dispatch('lead.update.before', $id);

        $data = $request->all();

        if (isset($data['lead_pipeline_stage_id'])) {
            $stage = $this->stageRepository->findOrFail($data['lead_pipeline_stage_id']);

            $data['lead_pipeline_id'] = $stage->lead_pipeline_id;
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();

            $stage = $pipeline->stages()->first();

            $data['lead_pipeline_id'] = $pipeline->id;

            $data['lead_pipeline_stage_id'] = $stage->id;
        }

        $data['person']['organization_id'] = empty($data['person']['organization_id']) ? null : $data['person']['organization_id'];

        $lead = $this->leadRepository->update($data, $id);

        Event::dispatch('lead.update.after', $lead);

        if (request()->ajax()) {
            return response()->json([
                'message' => trans('admin::app.leads.update-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.leads.update-success'));

        if (request()->has('closed_at')) {
            return redirect()->back();
        } else {
            return redirect()->route('admin.leads.index', $data['lead_pipeline_id']);
        }
    }

    /**
     * Update the lead attributes.
     */
    public function updateAttributes(int $id)
    {
        $data = request()->all();

        $attributes = $this->attributeRepository->findWhere([
            'entity_type' => 'leads',
            ['code', 'NOTIN', ['title', 'description']],
        ]);

        Event::dispatch('lead.update.before', $id);

        $lead = $this->leadRepository->update($data, $id, $attributes);

        Event::dispatch('lead.update.after', $lead);

        return response()->json([
            'message' => trans('admin::app.leads.update-success'),
        ]);
    }

    /**
     * Update the lead stage.
     */
    public function updateStage(int $id)
    {
        $this->validate(request(), [
            'lead_pipeline_stage_id' => 'required',
        ]);

        $lead = $this->leadRepository->findOrFail($id);

        $stage = $lead->pipeline->stages()
            ->where('id', request()->input('lead_pipeline_stage_id'))
            ->firstOrFail();

        Event::dispatch('lead.update.before', $id);

        $lead = $this->leadRepository->update(
            [
                'entity_type'            => 'leads',
                'lead_pipeline_stage_id' => $stage->id,
            ],
            $id,
            ['lead_pipeline_stage_id']
        );

        Event::dispatch('lead.update.after', $lead);

        return response()->json([
            'message' => trans('admin::app.leads.update-success'),
        ]);
    }

    /**
     * Search person results.
     */
    public function search(): AnonymousResourceCollection
    {
        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $results = $this->leadRepository
                ->pushCriteria(app(RequestCriteria::class))
                ->findWhereIn('user_id', $userIds);
        } else {
            $results = $this->leadRepository
                ->pushCriteria(app(RequestCriteria::class))
                ->all();
        }

        return LeadResource::collection($results);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->leadRepository->findOrFail($id);

        try {
            Event::dispatch('lead.delete.before', $id);

            $this->leadRepository->delete($id);

            Event::dispatch('lead.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.leads.destroy-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.leads.destroy-failed'),
            ], 400);
        }
    }

    /**
     * Mass Update the specified resources.
     */
    public function massUpdate(MassUpdateRequest $massUpdateRequest): JsonResponse
    {
        $leads = $this->leadRepository->findWhereIn('id', $massUpdateRequest->input('indices'));

        try {
            foreach ($leads as $lead) {
                Event::dispatch('lead.update.before', $lead->id);

                $lead = $this->leadRepository->find($lead->id);

                $lead?->update(['lead_pipeline_stage_id' => $massUpdateRequest->input('value')]);

                Event::dispatch('lead.update.before', $lead->id);
            }

            return response()->json([
                'message' => trans('admin::app.leads.update-success'),
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'message' => trans('admin::app.leads.update-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $leads = $this->leadRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        try {
            foreach ($leads as $lead) {
                Event::dispatch('lead.delete.before', $lead->id);

                $this->leadRepository->delete($lead->id);

                Event::dispatch('lead.delete.after', $lead->id);
            }

            return response()->json([
                'message' => trans('admin::app.leads.destroy-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.leads.destroy-failed'),
            ]);
        }
    }

    /**
     * Attach product to lead.
     */
    public function addProduct(int $leadId): JsonResponse
    {
        $product = $this->productRepository->updateOrCreate(
            [
                'lead_id'    => $leadId,
                'product_id' => request()->input('product_id'),
            ],
            array_merge(
                request()->all(),
                [
                    'lead_id' => $leadId,
                    'amount'  => request()->input('price') * request()->input('quantity'),
                ],
            )
        );

        return response()->json([
            'data'    => $product,
            'message' => trans('admin::app.leads.update-success'),
        ]);
    }

    /**
     * Remove product attached to lead.
     */
    public function removeProduct(int $id): JsonResponse
    {
        try {
            Event::dispatch('lead.product.delete.before', $id);

            $this->productRepository->deleteWhere([
                'lead_id'    => $id,
                'product_id' => request()->input('product_id'),
            ]);

            Event::dispatch('lead.product.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.leads.destroy-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.leads.destroy-failed'),
            ]);
        }
    }

    /**
     * Kanban lookup.
     */
    public function kanbanLookup()
    {
        $params = $this->validate(request(), [
            'column'      => ['required'],
            'search'      => ['required', 'min:2'],
        ]);

        /**
         * Finding the first column from the collection.
         */
        $column = collect($this->getKanbanColumns())->where('index', $params['column'])->firstOrFail();

        /**
         * Fetching on the basis of column options.
         */
        return app($column['filterable_options']['repository'])
            ->select([$column['filterable_options']['column']['label'].' as label', $column['filterable_options']['column']['value'].' as value'])
            ->where($column['filterable_options']['column']['label'], 'LIKE', '%'.$params['search'].'%')
            ->get()
            ->map
            ->only('label', 'value');
    }

    /**
     * Get columns for the kanban view.
     */
    private function getKanbanColumns(): array
    {
        return [
            [
                'index'                 => 'id',
                'label'                 => trans('admin::app.leads.index.kanban.columns.id'),
                'type'                  => 'integer',
                'searchable'            => false,
                'search_field'          => 'in',
                'filterable'            => true,
                'filterable_type'       => null,
                'filterable_options'    => [],
                'allow_multiple_values' => true,
                'sortable'              => true,
                'visibility'            => true,
            ],
            [
                'index'                 => 'lead_value',
                'label'                 => trans('admin::app.leads.index.kanban.columns.lead-value'),
                'type'                  => 'string',
                'searchable'            => false,
                'search_field'          => 'in',
                'filterable'            => true,
                'filterable_type'       => null,
                'filterable_options'    => [],
                'allow_multiple_values' => true,
                'sortable'              => true,
                'visibility'            => true,
            ],
            [
                'index'                 => 'user_id',
                'label'                 => trans('admin::app.leads.index.kanban.columns.sales-person'),
                'type'                  => 'string',
                'searchable'            => false,
                'search_field'          => 'in',
                'filterable'            => true,
                'filterable_type'       => 'searchable_dropdown',
                'filterable_options'    => [
                    'repository' => UserRepository::class,
                    'column'     => [
                        'label' => 'name',
                        'value' => 'id',
                    ],
                ],
                'allow_multiple_values' => true,
                'sortable'              => true,
                'visibility'            => true,
            ],
            [
                'index'                 => 'person.id',
                'label'                 => trans('admin::app.leads.index.kanban.columns.contact-person'),
                'type'                  => 'string',
                'searchable'            => false,
                'search_field'          => 'in',
                'filterable'            => true,
                'filterable_options'    => [],
                'allow_multiple_values' => true,
                'sortable'              => true,
                'visibility'            => true,
                'filterable_type'       => 'searchable_dropdown',
                'filterable_options'    => [
                    'repository' => PersonRepository::class,
                    'column'     => [
                        'label' => 'name',
                        'value' => 'id',
                    ],
                ],
            ],
            [
                'index'                 => 'lead_type_id',
                'label'                 => trans('admin::app.leads.index.kanban.columns.lead-type'),
                'type'                  => 'string',
                'searchable'            => false,
                'search_field'          => 'in',
                'filterable'            => true,
                'filterable_type'       => 'dropdown',
                'filterable_options'    => $this->typeRepository->all(['name as label', 'id as value'])->toArray(),
                'allow_multiple_values' => true,
                'sortable'              => true,
                'visibility'            => true,
            ],
            [
                'index'                 => 'lead_source_id',
                'label'                 => trans('admin::app.leads.index.kanban.columns.source'),
                'type'                  => 'string',
                'searchable'            => false,
                'search_field'          => 'in',
                'filterable'            => true,
                'filterable_type'       => 'dropdown',
                'filterable_options'    => $this->sourceRepository->all(['name as label', 'id as value'])->toArray(),
                'allow_multiple_values' => true,
                'sortable'              => true,
                'visibility'            => true,
            ],

            [
                'index'                 => 'tags.name',
                'label'                 => trans('admin::app.leads.index.kanban.columns.tags'),
                'type'                  => 'string',
                'searchable'            => false,
                'search_field'          => 'in',
                'filterable'            => true,
                'filterable_options'    => [],
                'allow_multiple_values' => true,
                'sortable'              => true,
                'visibility'            => true,
                'filterable_type'       => 'searchable_dropdown',
                'filterable_options'    => [
                    'repository' => TagRepository::class,
                    'column'     => [
                        'label' => 'name',
                        'value' => 'name',
                    ],
                ],
            ],

            [
                'index'              => 'expected_close_date',
                'label'              => trans('admin::app.leads.index.kanban.columns.expected-close-date'),
                'type'               => 'date',
                'searchable'         => false,
                'searchable'         => false,
                'sortable'           => true,
                'filterable'         => true,
                'filterable_type'    => 'date_range',
                'filterable_options' => DateRangeOptionEnum::options(),
            ],

            [
                'index'              => 'created_at',
                'label'              => trans('admin::app.leads.index.kanban.columns.created-at'),
                'type'               => 'date',
                'searchable'         => false,
                'searchable'         => false,
                'sortable'           => true,
                'filterable'         => true,
                'filterable_type'    => 'date_range',
                'filterable_options' => DateRangeOptionEnum::options(),
            ],
        ];
    }
}
