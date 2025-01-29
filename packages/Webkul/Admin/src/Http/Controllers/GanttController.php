<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

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
                        'start_date' => Carbon::parse($licitacion->start_date)->format('Y-m-d'),
                        'end_date' => Carbon::parse($licitacion->end_date)->format('Y-m-d'),
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
                        'start_date' => Carbon::parse($task->start_date)->format('Y-m-d'),
                        'end_date' => Carbon::parse($task->end_date)->format('Y-m-d'),
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
} 