<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\MetricRepository;
use Webkul\Admin\Http\Requests\MetricForm;
use Wbkul\Admin\Http\Controllers\Lead\LeadController;


class MetricController extends Controller
{
    /**
    * Constructor.
    */
    public function __construct(
        protected MetricRepository $metricRepository,
        protected LeadRepository $leadRepository,
    ) {}

     /**
     * Create a new metric for a specific lead.
     *
     * @param Request $request
     * @param int $leadId
     * @return JsonResponse
     */
    public function store(Request $request, int $leadId)
    {
        // Validate the input
                // Validate the input
        $validated = $request->validate([
            'capacidad_financiera'   => 'required|numeric|min:0|max:10',
            'capacidad_tecnica'      => 'required|numeric|min:0|max:10',
            'inteligencia_precios'   => 'required|numeric|min:0|max:10',
            'experiencia_servicios'  => 'required|numeric|min:0|max:10',
            'reputacion_mur'         => 'required|numeric|min:0|max:10',
            'conocimiento_costos'    => 'required|numeric|min:0|max:10',
            'cumplimiento_norma'     => 'required|numeric|min:0|max:10',
            'relacion_cliente'       => 'required|numeric|min:0|max:10',
            'innovacion'             => 'required|numeric|min:0|max:10',
        ]);
        
        // Ensure the lead exists
        $lead = $this->leadRepository->findOrFail($leadId);

        // Create the metric for the lead
        $metric = $this->metricRepository->create(array_merge($validated, [
            'lead_id' => $lead->id,
        ]));

        $allMetrics = $this->metricRepository->findWhere(['lead_id' => $lead->id]);

        return response()->json([
            'message' => 'Metric created successfully.',
            'data'    => $allMetrics,
        ]);
    }

    /**
    * Update an existing metric for a specific lead.
    *
    * @param Request $request
    * @param int $leadId
    * @return JsonResponse
    */
    public function update(Request $request, int $leadId)
    {
        // Validate the input
        $validated = $request->validate([
            'capacidad_financiera'   => 'nullable|numeric|min:0|max:10',
            'capacidad_tecnica'      => 'nullable|numeric|min:0|max:10',
            'inteligencia_precios'   => 'nullable|numeric|min:0|max:10',
            'experiencia_servicios'  => 'nullable|numeric|min:0|max:10',
            'reputacion_mur'         => 'nullable|numeric|min:0|max:10',
            'conocimiento_costos'    => 'nullable|numeric|min:0|max:10',
            'cumplimiento_norma'     => 'nullable|numeric|min:0|max:10',
            'relacion_cliente'       => 'required|numeric|min:0|max:10',
            'innovacion'             => 'required|numeric|min:0|max:10',
        ]);

        // Ensure the lead exists
        $lead = $this->leadRepository->findOrFail($leadId);

        // Ensure the metric exists for the lead
        $metric = $this->metricRepository->findWhere(['lead_id' => $lead->id])->first();

        if (!$metric) {
            return response()->json([
                'message' => 'Metric not found for this lead.',
            ], 404);
        }

        // Update the metric
        $this->metricRepository->update($validated, $metric->id);

        return response()->json([
            'message' => 'Metric created successfully.',
            'data'    => $metric,
        ]);
    }

}
