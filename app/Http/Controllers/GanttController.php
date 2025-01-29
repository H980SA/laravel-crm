<?php

namespace App\Http\Controllers;

use App\Models\Gannt;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    public function index()
    {
        return view('gantt.index');
    }

    public function getData()
    {
        $tasks = Gannt::orderBy('start_date')->get()->map(function($task) {
            return [
                'id' => $task->id,
                'text' => $task->etapa,
                'start_date' => date('Y-m-d H:i:s', strtotime($task->start_date)),
                'end_date' => date('Y-m-d H:i:s', strtotime($task->finish_date)),
                'progress' => $task->progreso,
                'priority' => $task->priority,
                'duration' => ceil((strtotime($task->finish_date) - strtotime($task->start_date)) / (60 * 60 * 24))
            ];
        });

        return response()->json([
            "data" => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $task = new Gannt();
        $task->etapa = $request->text;
        $task->start_date = $request->start_date;
        $task->finish_date = $request->end_date;
        $task->progreso = $request->progress;
        $task->priority = $request->priority;
        $task->save();

        return response()->json([
            "action" => "inserted",
            "tid" => $task->id
        ]);
    }

    public function update($id, Request $request)
    {
        $task = Gannt::find($id);
        $task->etapa = $request->text;
        $task->start_date = $request->start_date;
        $task->finish_date = $request->end_date;
        $task->progreso = $request->progress;
        $task->priority = $request->priority;
        $task->save();

        return response()->json([
            "action" => "updated"
        ]);
    }

    public function destroy($id)
    {
        $task = Gannt::find($id);
        $task->delete();

        return response()->json([
            "action" => "deleted"
        ]);
    }
} 