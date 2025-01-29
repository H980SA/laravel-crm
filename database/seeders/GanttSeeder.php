<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GanttSeeder extends Seeder
{
    public function run(): void
    {
        $leads = DB::table('leads')->get();
        
        if ($leads->isEmpty()) {
            echo "No hay leads en la base de datos.\n";
            return;
        }

        foreach ($leads as $lead) {
            // Verificar si ya existe un gantt para este lead
            $existingGantt = DB::table('gantts')
                ->where('lead_id', $lead->id)
                ->where('is_parent', true)
                ->first();

            if ($existingGantt) {
                echo "Ya existen tareas Gantt para el lead: {$lead->title}\n";
                continue;
            }

            $startDate = Carbon::parse($lead->expected_close_date)->subDays(30);
            $duration = 30; // Duración fija de 30 días hasta expected_close_date
            
            // Tarea padre
            $parentId = DB::table('gantts')->insertGetId([
                'text' => "Proyecto: {$lead->title}",
                'start_date' => $startDate,
                'end_date' => Carbon::parse($lead->expected_close_date),
                'duration' => $duration,
                'progress' => 0.00,
                'priority' => 'Media',
                'is_parent' => true,
                'parent_id' => null,
                'lead_id' => $lead->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Subtareas con duraciones más realistas
            $subTasks = [
                ['nombre' => 'Inicio de Proyecto', 'dias' => 5],
                ['nombre' => 'Planificación', 'dias' => 7],
                ['nombre' => 'Ejecución', 'dias' => 10],
                ['nombre' => 'Control y Seguimiento', 'dias' => 5],
                ['nombre' => 'Cierre', 'dias' => 3]
            ];

            $currentDate = $startDate;
            foreach ($subTasks as $task) {
                DB::table('gantts')->insert([
                    'text' => "{$task['nombre']} - {$lead->title}",
                    'start_date' => $currentDate,
                    'end_date' => $currentDate->copy()->addDays($task['dias']),
                    'duration' => $task['dias'],
                    'progress' => 0.00,
                    'priority' => 'Media',
                    'is_parent' => false,
                    'parent_id' => $parentId,
                    'lead_id' => $lead->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $currentDate = $currentDate->copy()->addDays($task['dias']);
            }
        }
    }
}