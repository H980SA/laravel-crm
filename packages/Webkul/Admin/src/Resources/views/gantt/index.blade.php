<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.gantt.index.title')
    </x-slot>

    <div id="gantt-app" v-cloak>
        <div class="flex gap-4 max-w-full">
            <!-- Gantt Section -->
            <div class="flex-1">
                <div class="flex flex-col gap-4">
                    <!-- Header Section -->
                    <div class="flex items-center justify-between bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm">
                        <div class="flex flex-col gap-2">
                            <div class="flex cursor-pointer items-center">
                                <!-- Breadcrumbs -->
                                <div class="flex justify-start max-lg:hidden">
                                    <div class="flex items-center gap-x-3.5">
                                        <nav aria-label="">
                                            <ol class="flex flex-wrap">
                                                <li class="flex items-center gap-x-1 text-sm font-normal text-brandColor dark:text-brandColor">
                                                    <a href="{{ route('admin.dashboard.index') }}">
                                                        Dashboard
                                                    </a>
                                                    <span class="after:content-['/'] ltr:mr-1 rtl:ml-1"></span>
                                                </li>

                                                <li class="flex items-center gap-x-1 text-base text-gray-600 after:content-['/'] last:cursor-default after:last:hidden dark:text-gray-300" aria-current="page">
                                                    @lang('admin::app.gantt.index.title')
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <div class="text-xl font-bold dark:text-white">
                                @lang('admin::app.gantt.index.title')
                            </div>
                        </div>
                    </div>

                    <!-- Gantt Component -->
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm">
                        <!-- Scale Controls -->
                        <div class="flex items-center gap-4 p-4 border-b border-gray-200 dark:border-gray-700">
                            <button 
                                v-for="item in [{value: 'day', label: 'Día'}, {value: 'week', label: 'Semana'}, {value: 'month', label: 'Mes'}]"
                                :key="item.value"
                                type="button"
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                :class="{'bg-blue-600 text-white': scale === item.value, 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200': scale !== item.value}"
                                @click="setScale(item.value)"
                            >
                                @{{ item.label }}
                            </button>
                        </div>
                        <div id="gantt_here" style="width:100%; height:600px;"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="w-96 flex-shrink-0">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-4">
                    <div v-if="selectedTask || isCreatingTask" class="task-form">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">@{{ isCreatingTask ? 'Nueva Tarea' : 'Editar Tarea' }}</h3>
                            <button @click="closeTaskForm" class="text-gray-400 hover:text-gray-600">&times;</button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                <input 
                                    type="text" 
                                    v-model="currentTask.text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>

                            <div v-if="isCreatingTask">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Licitación</label>
                                <select 
                                    v-model="currentTask.parent"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Seleccione una licitación</option>
                                    <option v-for="lic in licitaciones" :key="lic.id" :value="lic.id">
                                        @{{ lic.text }}
                                    </option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Inicio</label>
                                    <input 
                                        type="date" 
                                        v-model="currentTask.start_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Fin</label>
                                    <input 
                                        type="date" 
                                        v-model="currentTask.end_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Progreso (%)</label>
                                <input 
                                    type="number" 
                                    v-model.number="currentTask.progress"
                                    min="0"
                                    max="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>

                            <div class="flex justify-end">
                                <button 
                                    @click="saveTask"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    @{{ isCreatingTask ? 'Crear Tarea' : 'Actualizar Tarea' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <h3 class="text-lg font-medium mb-2">Gestión de Tareas</h3>
                        <p class="text-gray-500 mb-4">Selecciona una tarea para editar o crea una nueva</p>
                        <button 
                            @click="startNewTask"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        >
                            + Nueva Tarea
                        </button>
                    </div>

                    <!-- Today's Tasks -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium mb-4">Tareas de Hoy</h3>
                        <div v-if="todayTasks && todayTasks.length" class="space-y-4">
                            <div 
                                v-for="task in todayTasks" 
                                :key="task.id"
                                class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium">@{{ task.text }}</h4>
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                        @{{ task.type === 'project' ? 'Licitación' : 'Tarea' }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <div>Inicio: @{{ formatDate(task.start_date) }}</div>
                                    <div>Fin: @{{ formatDate(task.end_date) }}</div>
                                </div>
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div 
                                            class="bg-blue-600 h-2.5 rounded-full" 
                                            :style="{ width: (task.progress * 100) + '%' }"
                                        ></div>
                                    </div>
                                    <div class="text-right text-xs mt-1">@{{ Math.round(task.progress * 100) }}%</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-gray-500">
                            No hay tareas programadas para hoy
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
        
        <script type="text/javascript">
            new Vue({
                el: '#gantt-app',
                
                data() {
                    return {
                        scale: 'week',
                        selectedTask: null,
                        isCreatingTask: false,
                        currentTask: {
                            text: '',
                            start_date: new Date().toISOString().split('T')[0],
                            end_date: new Date().toISOString().split('T')[0],
                            progress: 0,
                            parent: ''
                        },
                        todayTasks: @json($todayTasks ?? []),
                        licitaciones: @json($licitaciones ?? []),
                        ganttInstance: null
                    }
                },

                mounted() {
                    this.$nextTick(() => {
                        this.initGantt();
                    });
                },

                methods: {
                    initGantt() {
                        // Configuración básica
                        gantt.config.date_format = "%Y-%m-%d";
                        gantt.config.scale_height = 60;
                        gantt.config.row_height = 40;
                        gantt.config.task_height = 20;
                        gantt.config.min_column_width = 40;
                        gantt.config.fit_tasks = true;

                        // Permitir interacción
                        gantt.config.readonly = false;
                        gantt.config.drag_links = false;
                        gantt.config.drag_progress = true;
                        gantt.config.drag_resize = true;
                        gantt.config.drag_move = true;

                        // Evento de clic en tarea
                        gantt.attachEvent("onTaskClick", (id) => {
                            const task = gantt.getTask(id);
                            this.selectedTask = task;
                            this.isCreatingTask = false;
                            this.currentTask = {
                                id: task.id,
                                text: task.text,
                                start_date: gantt.date.date_to_str(task.start_date),
                                end_date: gantt.date.date_to_str(task.end_date),
                                progress: Math.round(task.progress * 100),
                                parent: task.parent
                            };
                            return true;
                        });

                        // Sombreado de fila seleccionada
                        gantt.templates.task_row_class = (start, end, task) => {
                            return this.selectedTask && this.selectedTask.id === task.id ? 'selected-row' : '';
                        };

                        // Configurar columnas
                        gantt.config.columns = [
                            {
                                name: "text",
                                label: "Pipeline Process",
                                tree: true,
                                width: 300,
                                resize: true
                            },
                            {
                                name: "start_date",
                                label: "Start",
                                align: "center",
                                width: 100,
                                template: function(task) {
                                    return gantt.date.date_to_str("%d/%m/%Y")(task.start_date);
                                }
                            },
                            {
                                name: "duration",
                                label: "Days",
                                align: "center",
                                width: 70,
                                template: function(task) {
                                    return task.duration + " días";
                                }
                            },
                            {
                                name: "priority",
                                label: "Priority",
                                align: "center",
                                width: 80
                            },
                            {
                                name: "progress",
                                label: "Progress %",
                                align: "center",
                                width: 100,
                                template: function(task) {
                                    return Math.round(task.progress * 100) + "%";
                                }
                            }
                        ];

                        // Configurar tooltips
                        gantt.templates.tooltip_text = function(start, end, task) {
                            return `<b>Tarea:</b> ${task.text}<br/>
                                    <b>Inicio:</b> ${gantt.templates.tooltip_date_format(start)}<br/>
                                    <b>Duración:</b> ${task.duration} días<br/>
                                    <b>Progreso:</b> ${Math.round(task.progress * 100)}%`;
                        };

                        // Inicializar Gantt
                        gantt.init('gantt_here');
                        this.ganttInstance = gantt;

                        // Establecer escala inicial
                        this.setScale(this.scale);

                        // Cargar datos
                        const tasks = @json($tasks ?? []);
                        if (tasks && tasks.length) {
                            this.ganttInstance.parse({
                                data: tasks
                            });
                        }
                    },

                    startNewTask() {
                        this.isCreatingTask = true;
                        this.selectedTask = null;
                        this.currentTask = {
                            text: '',
                            start_date: new Date().toISOString().split('T')[0],
                            end_date: new Date().toISOString().split('T')[0],
                            progress: 0,
                            parent: ''
                        };
                    },

                    closeTaskForm() {
                        this.selectedTask = null;
                        this.isCreatingTask = false;
                        this.currentTask = {
                            text: '',
                            start_date: new Date().toISOString().split('T')[0],
                            end_date: new Date().toISOString().split('T')[0],
                            progress: 0,
                            parent: ''
                        };
                    },

                    saveTask() {
                        const taskData = {
                            ...this.currentTask,
                            progress: this.currentTask.progress / 100
                        };

                        if (this.isCreatingTask) {
                            // Agregar nueva tarea al Gantt
                            const newTask = {
                                ...taskData,
                                id: Date.now(), // ID temporal
                                duration: this.calculateDuration(taskData.start_date, taskData.end_date)
                            };
                            this.ganttInstance.addTask(newTask);
                        } else {
                            // Actualizar tarea existente
                            const task = this.ganttInstance.getTask(taskData.id);
                            Object.assign(task, taskData);
                            this.ganttInstance.updateTask(task.id);
                        }

                        this.closeTaskForm();
                        this.ganttInstance.render();
                    },

                    calculateDuration(start, end) {
                        const startDate = new Date(start);
                        const endDate = new Date(end);
                        return Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                    },

                    formatDate(date) {
                        if (!date) return '';
                        return new Date(date).toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    },

                    setScale(scale) {
                        this.scale = scale;
                        if (this.ganttInstance) {
                            switch (scale) {
                                case 'day':
                                    this.ganttInstance.config.scales = [
                                        {unit: "month", step: 1, format: "%F %Y"},
                                        {unit: "day", step: 1, format: "%j"}
                                    ];
                                    this.ganttInstance.config.min_column_width = 30;
                                    break;
                                case 'week':
                                    this.ganttInstance.config.scales = [
                                        {unit: "month", step: 1, format: "%F %Y"},
                                        {
                                            unit: "week", step: 1,
                                            format: function(date) {
                                                return "Sem " + gantt.date.getWeek(date);
                                            }
                                        }
                                    ];
                                    this.ganttInstance.config.min_column_width = 50;
                                    break;
                                case 'month':
                                    this.ganttInstance.config.scales = [
                                        {unit: "year", step: 1, format: "%Y"},
                                        {unit: "month", step: 1, format: "%F"}
                                    ];
                                    this.ganttInstance.config.min_column_width = 100;
                                    break;
                            }
                            this.ganttInstance.render();
                        }
                    }
                }
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
        <style>
            [v-cloak] { display: none; }
            
            .gantt_container {
                font-family: var(--font-family);
                font-size: var(--font-size-base);
                border-radius: 0.5rem;
            }

            .gantt_grid_scale, 
            .gantt_task_scale {
                background-color: #f8fafc;
                color: #64748b;
            }

            .gantt_scale_cell {
                font-weight: 500;
            }

            .dark .gantt_grid_scale,
            .dark .gantt_task_scale {
                background-color: #1e293b;
                color: #94a3b8;
            }

            .gantt_task_progress {
                background-color: #3b82f6;
            }

            .gantt_task_line {
                background-color: #60a5fa;
                border-color: #3b82f6;
            }

            .gantt_task_line.gantt_project {
                background-color: #f59e0b;
                border-color: #d97706;
            }

            .dark .gantt_task_line {
                background-color: #3b82f6;
                border-color: #2563eb;
            }

            .dark .gantt_task_line.gantt_project {
                background-color: #d97706;
                border-color: #b45309;
            }

            .selected-row {
                background-color: rgba(59, 130, 246, 0.1);
            }
        </style>
    @endPushOnce
</x-admin::layouts> 