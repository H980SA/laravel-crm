<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.gantt.index.title')
    </x-slot>

    <div id="gantt-app" v-cloak>
        <div class="flex flex-col gap-4 max-w-full">
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

                <!-- Action Buttons -->
                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        @if (bouncer()->hasPermission('gantt.create'))
                            <button 
                                type="button"
                                class="primary-button"
                                @click="$refs.ganttComponent.openCreateModal()"
                            >
                                @lang('admin::app.gantt.index.create-btn')
                            </button>
                        @endif
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

                <!-- Gantt Component -->
                <gantt-chart
                    :tasks="{{ json_encode($tasks) }}"
                    :licitaciones="{{ json_encode($licitaciones) }}"
                    :today-tasks="{{ json_encode($todayTasks) }}"
                ></gantt-chart>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <!-- Vue.js -->
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <!-- DHTMLX Gantt -->
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
        
        <script type="text/javascript">
            Vue.component('gantt-chart', {
                template: '#gantt-chart-template',

                props: {
                    tasks: {
                        type: Array,
                        required: true
                    },
                    licitaciones: {
                        type: Array,
                        required: true
                    },
                    todayTasks: {
                        type: Array,
                        required: true
                    }
                },

                data() {
                    return {
                        scale: 'week',
                        ganttInstance: null
                    }
                },

                mounted() {
                    console.log('Component mounted');
                    console.log('Tasks:', this.tasks);
                    console.log('Licitaciones:', this.licitaciones);
                    
                    this.$nextTick(() => {
                        this.initGantt();
                    });
                },

                methods: {
                    initGantt() {
                        console.log('Initializing Gantt');
                        
                        // Configuración básica
                        gantt.config.date_format = "%Y-%m-%d";
                        gantt.config.scale_height = 60;
                        gantt.config.row_height = 40;
                        gantt.config.task_height = 20;
                        gantt.config.min_column_width = 40;

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
                                name: "end_date",
                                label: "End",
                                align: "center",
                                width: 100,
                                template: function(task) {
                                    return gantt.date.date_to_str("%d/%m/%Y")(task.end_date);
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
                        gantt.init(this.$refs.ganttContainer);
                        this.ganttInstance = gantt;

                        // Establecer vista inicial
                        this.setScale('week');
                        
                        // Cargar datos
                        if (this.tasks && this.tasks.length) {
                            console.log('Loading tasks:', this.tasks);
                            this.ganttInstance.parse({
                                data: this.tasks
                            });
                            this.ganttInstance.render();
                        } else {
                            console.log('No tasks to load');
                        }
                    },

                    setScale(viewType) {
                        console.log('Setting scale to:', viewType);
                        if (!this.ganttInstance) {
                            console.error('Gantt instance not initialized');
                            return;
                        }
                        
                        this.scale = viewType;
                        
                        switch (viewType) {
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
                                            var weekNum = gantt.date.getWeek(date);
                                            return "Sem " + weekNum;
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
            });

            new Vue({
                el: '#gantt-app'
            });
        </script>
    @endPushOnce

    <!-- Template del componente Gantt -->
    <script type="text/x-template" id="gantt-chart-template">
        <div>
            <div class="flex items-center gap-4 mb-4 p-4 bg-white rounded-lg shadow dark:bg-gray-800">
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
            <div ref="ganttContainer" class="bg-white rounded-lg shadow dark:bg-gray-800" style="width:100%; height:600px;"></div>
        </div>
    </script>

    @pushOnce('styles')
        <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" type="text/css">
        <style>
            [v-cloak] {
                display: none;
            }
            
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
        </style>
    @endPushOnce
</x-admin::layouts> 