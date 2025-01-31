<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;

class GanttController extends Controller
{
    /**
     * Display Gantt view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Obtener las tareas principales (padres) con el título del lead
            $licitaciones = DB::table('gantts')
                ->leftJoin('leads', 'gantts.lead_id', '=', 'leads.id')
                ->where('gantts.is_parent', true)
                ->select([
                    'gantts.id',
                    'gantts.text',
                    'gantts.start_date',
                    'gantts.end_date',
                    'gantts.progress',
                    'leads.title as lead_title'
                ])
                ->get()
                ->map(function ($licitacion) {
                    return [
                        'id' => $licitacion->id,
                        'text' => $licitacion->lead_title ?? $licitacion->text,
                        'start_date' => Carbon::parse($licitacion->start_date)->format('Y-m-d H:i'),
                        'end_date' => Carbon::parse($licitacion->end_date)->format('Y-m-d H:i'),
                        'duration' => Carbon::parse($licitacion->start_date)
                            ->diffInDays(Carbon::parse($licitacion->end_date)) + 1,
                        'progress' => floatval($licitacion->progress),
                        'open' => true,
                        'type' => 'project'
                    ];
                })
                ->values()
                ->toArray();

            // Obtener las subtareas
            $tasks = DB::table('gantts')
                ->where('is_parent', false)
                ->orderBy('start_date')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => 'task_' . $task->id,
                        'text' => $task->text,
                        'start_date' => Carbon::parse($task->start_date)->format('Y-m-d H:i'),
                        'end_date' => Carbon::parse($task->end_date)->format('Y-m-d H:i'),
                        'duration' => Carbon::parse($task->start_date)
                            ->diffInDays(Carbon::parse($task->end_date)) + 1,
                        'parent' => $task->parent_id,
                        'progress' => floatval($task->progress),
                        'priority' => $task->priority,
                        'open' => true
                    ];
                })
                ->values()
                ->toArray();

            // Combinar licitaciones y tareas
            $allTasks = array_merge($licitaciones, $tasks);

            // Debug: Imprimir datos para verificar
            \Log::info('Datos del Gantt:', [
                'licitaciones' => $licitaciones,
                'tasks' => $tasks,
                'allTasks' => $allTasks
            ]);

            return view('admin::gantt.index', [
                'tasks' => $allTasks,
                'licitaciones' => $licitaciones,
                'todayTasks' => collect($tasks)
                    ->filter(function ($task) {
                        return Carbon::parse($task['start_date'])->isToday();
                    })
                    ->values()
                    ->toArray()
            ]);
        } catch (Exception $e) {
            \Log::error('Error en GanttController: ' . $e->getMessage());
            
            return view('admin::gantt.index', [
                'tasks' => [],
                'licitaciones' => [],
                'todayTasks' => [],
                'error' => 'No se pudieron cargar los datos del diagrama Gantt: ' . $e->getMessage()
            ]);
        }
    }

    public function getData()
    {
        try {
            // Obtener las tareas principales (padres) con el título del lead
            $licitaciones = DB::table('gantts')
                ->leftJoin('leads', 'gantts.lead_id', '=', 'leads.id')
                ->where('gantts.is_parent', true)
                ->select([
                    'gantts.id',
                    'gantts.text',
                    'gantts.start_date',
                    'gantts.end_date',
                    'gantts.progress',
                    'leads.title as lead_title'
                ])
                ->get()
                ->map(function ($licitacion) {
                    return [
                        'id' => $licitacion->id,
                        'text' => $licitacion->lead_title ?? $licitacion->text,
                        'start_date' => Carbon::parse($licitacion->start_date)->format('Y-m-d H:i'),
                        'end_date' => Carbon::parse($licitacion->end_date)->format('Y-m-d H:i'),
                        'duration' => Carbon::parse($licitacion->start_date)
                            ->diffInDays(Carbon::parse($licitacion->end_date)) + 1,
                        'progress' => floatval($licitacion->progress),
                        'open' => true,
                        'type' => 'project'
                    ];
                })
                ->values()
                ->toArray();

            // Obtener las subtareas
            $tasks = DB::table('gantts')
                ->where('is_parent', false)
                ->orderBy('start_date')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => 'task_' . $task->id,
                        'text' => $task->text,
                        'start_date' => Carbon::parse($task->start_date)->format('Y-m-d H:i'),
                        'end_date' => Carbon::parse($task->end_date)->format('Y-m-d H:i'),
                        'duration' => Carbon::parse($task->start_date)
                            ->diffInDays(Carbon::parse($task->end_date)) + 1,
                        'parent' => $task->parent_id,
                        'progress' => floatval($task->progress),
                        'priority' => $task->priority,
                        'open' => true
                    ];
                })
                ->values()
                ->toArray();

            return response()->json([
                'tasks' => array_merge($licitaciones, $tasks)
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLicitaciones()
    {
        try {
            $licitaciones = DB::table('gantts')
                ->leftJoin('leads', 'gantts.lead_id', '=', 'leads.id')
                ->where('gantts.is_parent', true)
                ->select([
                    'gantts.id',
                    'gantts.text',
                    'gantts.start_date',
                    'gantts.end_date',
                    'gantts.progress',
                    'leads.title as lead_title'
                ])
                ->get()
                ->map(function ($licitacion) {
                    return [
                        'id' => $licitacion->id,
                        'text' => $licitacion->lead_title ?? $licitacion->text,
                        'start_date' => Carbon::parse($licitacion->start_date)->format('Y-m-d H:i'),
                        'end_date' => Carbon::parse($licitacion->end_date)->format('Y-m-d H:i'),
                        'duration' => Carbon::parse($licitacion->start_date)
                            ->diffInDays(Carbon::parse($licitacion->end_date)) + 1,
                        'progress' => floatval($licitacion->progress),
                        'open' => true,
                        'type' => 'project'
                    ];
                })
                ->values()
                ->toArray();

            return response()->json($licitaciones);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store()
    {
        try {
            $data = request()->validate([
                'text' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'progress' => 'required|numeric|min:0|max:1',
                'parent' => 'nullable',
                'type' => 'required|string|in:task,project',
                'priority' => 'nullable|string|in:Alta,Media,Baja'
            ]);

            // Determinar si es una tarea padre (licitación) o una subtarea
            $isParent = $data['type'] === 'project';
            
            // Calcular la duración
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $duration = $startDate->diffInDays($endDate) + 1;

            // Procesar el parent_id
            $parentId = null;
            if (!$isParent && isset($data['parent'])) {
                $parentId = is_numeric($data['parent']) ? 
                    $data['parent'] : 
                    ltrim($data['parent'], 'task_');
            }
            
            // Preparar los datos para la inserción
            $taskData = [
                'text' => $data['text'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'duration' => $duration,
                'progress' => $data['progress'],
                'priority' => $data['priority'] ?? 'Media',
                'is_parent' => $isParent,
                'parent_id' => $parentId,
                'lead_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];

            \Log::info('Creando tarea con datos:', $taskData);

            $taskId = DB::table('gantts')->insertGetId($taskData);

            $responseTask = [
                'id' => $isParent ? $taskId : 'task_' . $taskId,
                'text' => $data['text'],
                'start_date' => $startDate->format('Y-m-d H:i'),
                'end_date' => $endDate->format('Y-m-d H:i'),
                'duration' => $duration,
                'progress' => floatval($data['progress']),
                'priority' => $taskData['priority'],
                'parent' => $isParent ? null : $data['parent'],
                'type' => $data['type']
            ];

            \Log::info('Tarea creada:', $responseTask);

            return response()->json([
                'success' => true,
                'task' => $responseTask,
                'message' => 'Tarea creada exitosamente'
            ]);
        } catch (ValidationException $e) {
            \Log::error('Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => $e->errors()[array_key_first($e->errors())][0],
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            \Log::error('Error al crear tarea:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la tarea: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update($id)
    {
        try {
            $data = request()->validate([
                'text' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'progress' => 'required|numeric|min:0|max:1',
                'type' => 'required|string|in:task,project',
                'priority' => 'nullable|string|in:Alta,Media,Baja'
            ]);

            // Remover el prefijo 'task_' si existe
            $taskId = str_starts_with($id, 'task_') ? substr($id, 5) : $id;

            \Log::info('Actualizando tarea:', [
                'id' => $taskId,
                'data' => $data
            ]);

            $updated = DB::table('gantts')
                ->where('id', $taskId)
                ->update([
                    'text' => $data['text'],
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'progress' => $data['progress'],
                    'priority' => $data['priority'] ?? 'Media',
                    'updated_at' => now()
                ]);

            if (!$updated) {
                throw new Exception('No se encontró la tarea para actualizar');
            }

            $responseTask = [
                'id' => $id,
                'text' => $data['text'],
                'start_date' => Carbon::parse($data['start_date'])->format('Y-m-d H:i'),
                'end_date' => Carbon::parse($data['end_date'])->format('Y-m-d H:i'),
                'progress' => floatval($data['progress']),
                'priority' => $data['priority'] ?? 'Media',
                'type' => $data['type']
            ];

            \Log::info('Tarea actualizada:', $responseTask);

            return response()->json([
                'success' => true,
                'task' => $responseTask,
                'message' => 'Tarea actualizada exitosamente'
            ]);
        } catch (ValidationException $e) {
            \Log::error('Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            \Log::error('Error al actualizar tarea:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la tarea: ' . $e->getMessage()
            ], 500);
        }
    }
} 