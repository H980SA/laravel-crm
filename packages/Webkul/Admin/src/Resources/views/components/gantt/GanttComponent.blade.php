@props(['tasks', 'licitaciones', 'todayTasks'])

<div class="flex flex-col gap-4 max-w-full">
    <!-- Breadcrumbs y título -->
    <div class="flex items-center justify-between bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <span class="text-gray-600 dark:text-gray-400">Dashboard</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-blue-600">Gantt Chart</span>
            </div>
            <div class="text-xl font-bold dark:text-white">
                @lang('admin::app.gantt.title')
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm">
        <!-- Controles de vista -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-4">
                <button onclick="changeView('day')" class="px-6 py-2 text-sm font-medium rounded-md view-button" data-view="day">
                    Día
                </button>
                <button onclick="changeView('week')" class="px-6 py-2 text-sm font-medium rounded-md view-button active" data-view="week">
                    Semana
                </button>
                <button onclick="changeView('month')" class="px-6 py-2 text-sm font-medium rounded-md view-button" data-view="month">
                    Mes
                </button>
            </div>
        </div>

        <!-- Contenedor del Gantt -->
        <div id="gantt_here" style="width:100%; height:600px;"></div>
    </div>
</div>

@include('admin::components.gantt.GanttSidebar', [
    'selectedTask' => $selectedTask ?? null,
    'isCreatingTask' => $isCreatingTask ?? false,
    'todayTasks' => $todayTasks ?? [],
    'licitaciones' => $licitaciones ?? []
])

<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
<link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración básica
    gantt.config.date_format = "%Y-%m-%d";  // Cambiado para coincidir con el formato de la BD
    gantt.config.scale_height = 60;
    gantt.config.row_height = 40;
    gantt.config.task_height = 20;
    gantt.config.min_column_width = 40;

    // Configurar columnas
    gantt.config.columns = [
        {
            name: "text",
            label: "Nombre del Proyecto/Tarea",
            tree: true,
            width: 300,
            resize: true
        },
        {
            name: "start_date",
            label: "Inicio",
            align: "center",
            width: 100
        },
        {
            name: "end_date",
            label: "Fin",
            align: "center",
            width: 100,
        },
        {
            name: "duration",
            label: "Duración (días)",
            align: "center",
            width: 70
        },
        {
            name: "priority",
            label: "Prioridad",
            align: "center",
            width: 80
        },
        {
            name: "progress",
            label: "Progreso",
            align: "center",
            width: 100,
            template: function(task) {
                return Math.round(task.progress * 100) + "%";
            }
        }
    ];

    // Configurar escalas
    function setScales(viewType) {
        switch (viewType) {
            case "day":
                gantt.config.scales = [
                    {unit: "month", step: 1, format: "%F %Y"},
                    {unit: "day", step: 1, format: "%j"}
                ];
                break;
            case "week":
                gantt.config.scales = [
                    {unit: "month", step: 1, format: "%F %Y"},
                    {
                        unit: "week", step: 1,
                        format: function(date) {
                            var weekNum = gantt.date.getWeek(date);
                            return "Sem " + weekNum;
                        }
                    }
                ];
                break;
            case "month":
                gantt.config.scales = [
                    {unit: "year", step: 1, format: "%Y"},
                    {unit: "month", step: 1, format: "%F"}
                ];
                break;
        }
        gantt.render();
    }

    // Inicializar Gantt
    gantt.init("gantt_here");

    // Cargar datos desde la variable PHP
    const tasks = @json($tasks);
    gantt.parse({data: tasks});

    // Establecer vista inicial
    setScales("month");

    // Configurar colores según prioridad
    gantt.templates.task_class = function(start, end, task) {
        switch (task.priority) {
            case "Alta":
                return "high-priority";
            case "Baja":
                return "low-priority";
            default:
                return "medium-priority";
        }
    };

    // Función para cambiar vista
    window.changeView = function(viewType) {
        const buttons = document.querySelectorAll('.view-button');
        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === viewType) {
                btn.classList.add('active');
            }
        });
        setScales(viewType);
    };
});
</script>

<style>
.gantt_task_line {
    border-radius: 3px;
    background-color: #4299e1;
    border-color: #3182ce;
}

.gantt_task_progress {
    background-color: #2c5282;
}

.gantt_grid_head_cell {
    font-weight: bold;
    color: #2d3748;
}

.gantt_grid_data {
    color: #4a5568;
}

.weekend {
    background: #f7fafc;
}

.today {
    background-color: #fef3c7;
}

/* Tema oscuro */
.dark .gantt_task_line {
    background-color: #3b82f6;
    border-color: #2563eb;
}

.dark .gantt_task_progress {
    background-color: #1d4ed8;
}

.dark .gantt_grid_head_cell {
    color: #e2e8f0;
    background-color: #1e293b;
}

.dark .gantt_grid_data {
    color: #cbd5e1;
    background-color: #0f172a;
}

.dark .weekend {
    background: #1e293b;
}

.dark .today {
    background-color: #422006;
}

.view-button {
    background-color: #f3f4f6;
    color: #374151;
    transition: all 0.2s;
}

.view-button:hover {
    background-color: #e5e7eb;
}

.view-button.active {
    background-color: #3b82f6;
    color: white;
}

.high-priority {
    background-color: #ff5252;
    border-color: #ff1744;
}

.medium-priority {
    background-color: #4299e1;
    border-color: #3182ce;
}

.low-priority {
    background-color: #68d391;
    border-color: #48bb78;
}

.gantt_task_progress {
    background-color: rgba(0, 0, 0, 0.2);
}

.gantt_task_line {
    border-radius: 3px;
}

.gantt_grid_head_cell {
    font-weight: bold;
    color: #2d3748;
}

.gantt_grid_data {
    color: #4a5568;
}
</style>