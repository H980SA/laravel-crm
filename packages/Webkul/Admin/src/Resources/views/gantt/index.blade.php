<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.gantt.index.title')
    </x-slot>

    <div id="gantt-app" v-cloak>
        <div class="flex gap-4 max-w-full">
            <!-- Gantt Section -->
            @include('admin::components.gantt.main-section')

            <!-- Sidebar -->
            @include('admin::components.gantt.sidebar')
        </div>
    </div>

    @pushOnce('scripts')
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
        <script type="text/javascript">
            new Vue({
                el: "#gantt-app",

                data() {
                    return {
                        isLoading: false,
                        tasks: [],
                        links: [],
                        selectedTask: null,
                        isCreatingTask: false,
                        currentTask: {
                            text: "",
                            start_date: "",
                            end_date: "",
                            progress: 0,
                            parent: "",
                            type: "task"
                        },
                        licitaciones: [],
                        todayTasks: [],
                        scale: 'week'
                    }
                },

                mounted() {
                    this.initGantt();
                    this.loadLicitaciones();
                    this.updateTodayTasks();
                },

                methods: {
                    initGantt() {
                        // Configuración inicial del Gantt
                        gantt.config.xml_date = "%Y-%m-%d %H:%i";
                        gantt.config.date_format = "%Y-%m-%d %H:%i";
                        
                        // Configuración de la escala
                        gantt.config.scale_height = 60;
                        gantt.config.row_height = 35;
                        gantt.config.task_height = 20;
                        gantt.config.grid_width = 380;
                        gantt.config.autosize = "y";
                        
                        // Configuración de columnas
                        gantt.config.columns = [
                            {name: "text", label: "Tarea", tree: true, width: '*', min_width: 200},
                            {name: "start_date", label: "Inicio", align: "center", width: 90},
                            {name: "duration", label: "Duración", align: "center", width: 60},
                            {name: "priority", label: "Prioridad", align: "center", width: 80}
                        ];
                        
                        // Configuración de la escala de tiempo
                        gantt.config.scales = [
                            {unit: "year", step: 1, format: "%Y"},
                            {unit: "month", step: 1, format: "%F"},
                            {unit: "day", step: 1, format: "%j", css: function(date) {
                                if(date.getDay() === 0 || date.getDay() === 6) return "weekend";
                            }}
                        ];

                        // Configuración de fechas límite
                        gantt.config.start_date = new Date(2025, 0, 1);
                        gantt.config.end_date = new Date(2025, 11, 31);

                        // Inicializar el Gantt
                        gantt.init("gantt_here");

                        // Cargar datos
                        this.loadData();

                        // Eventos
                        gantt.attachEvent("onTaskClick", (id) => {
                            this.selectTask(id);
                            return true;
                        });
                    },

                    async loadData() {
                        try {
                            this.isLoading = true;
                            const response = await fetch("/admin/gantt/data");
                            const data = await response.json();
                            
                            if (data.tasks) {
                                this.tasks = data.tasks;
                                gantt.clearAll();
                                gantt.parse({data: this.tasks});
                                gantt.render();
                            }
                        } catch (error) {
                            console.error("Error loading Gantt data:", error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async loadLicitaciones() {
                        try {
                            const response = await fetch("/admin/gantt/licitaciones");
                            const data = await response.json();
                            this.licitaciones = data || [];
                        } catch (error) {
                            console.error("Error loading licitaciones:", error);
                        }
                    },

                    selectTask(taskId) {
                        this.selectedTask = gantt.getTask(taskId);
                        this.currentTask = { ...this.selectedTask };
                        this.isCreatingTask = false;
                    },

                    startNewTask() {
                        const today = new Date();
                        const tomorrow = new Date(today);
                        tomorrow.setDate(tomorrow.getDate() + 1);

                        this.isCreatingTask = true;
                        this.selectedTask = null;
                        this.currentTask = {
                            text: "",
                            start_date: today.toISOString().split("T")[0],
                            end_date: tomorrow.toISOString().split("T")[0],
                            progress: 0,
                            parent: "",
                            type: "task"
                        };
                    },

                    closeTaskForm() {
                        this.selectedTask = null;
                        this.isCreatingTask = false;
                        this.currentTask = {
                            text: "",
                            start_date: "",
                            end_date: "",
                            progress: 0,
                            parent: "",
                            type: "task"
                        };
                    },

                    async saveTask() {
                        try {
                            const taskData = {
                                ...this.currentTask,
                                progress: this.currentTask.progress / 100,
                            };

                            const url = this.isCreatingTask
                                ? "/api/admin/gantt/tasks"
                                : `/api/admin/gantt/tasks/${this.selectedTask.id}`;

                            const method = this.isCreatingTask ? "POST" : "PUT";

                            const response = await fetch(url, {
                                method: method,
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector(
                                        'meta[name="csrf-token"]'
                                    ).content,
                                },
                                body: JSON.stringify(taskData),
                            });

                            if (!response.ok) throw new Error("Failed to save task");

                            await this.loadData();
                            this.closeTaskForm();
                        } catch (error) {
                            console.error("Error saving task:", error);
                        }
                    },

                    updateTodayTasks() {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        this.todayTasks = this.tasks.filter((task) => {
                            const taskDate = new Date(task.start_date);
                            taskDate.setHours(0, 0, 0, 0);
                            return taskDate.getTime() === today.getTime();
                        });
                    },

                    formatDate(dateStr) {
                        return new Date(dateStr).toLocaleDateString();
                    },

                    // Add scale change method
                    changeView(value) {
                        this.scale = value;
                        this.setScale(value);
                    },

                    // Add scale setting method
                    setScale(value) {
                        switch (value) {
                            case 'day':
                                gantt.config.scales = [
                                    {unit: "year", step: 1, format: "%Y"},
                                    {unit: "month", step: 1, format: "%F"},
                                    {unit: "day", step: 1, format: "%j"}
                                ];
                                break;
                            case 'week':
                                gantt.config.scales = [
                                    {unit: "year", step: 1, format: "%Y"},
                                    {unit: "month", step: 1, format: "%F"},
                                    {unit: "week", step: 1, format: "Semana #%W"}
                                ];
                                break;
                            case 'month':
                                gantt.config.scales = [
                                    {unit: "year", step: 1, format: "%Y"},
                                    {unit: "month", step: 1, format: "%F"},
                                    {unit: "week", step: 1, format: "Sem #%W"}
                                ];
                                break;
                        }
                        gantt.render();
                    },
                }
            });
        </script>
        @include('admin::components.gantt.scripts')
    @endPushOnce

    @pushOnce('styles')
        <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
        <style>
            .gantt_task_line {
                border-radius: 3px;
            }
            
            .gantt_task_line.gantt_project {
                background-color: #3498db;
                border-color: #2980b9;
            }
            
            .gantt_task_line.weekend {
                background-color: #f1c40f;
            }
            
            .gantt_grid_scale,
            .gantt_task_scale {
                background-color: #f8f9fa;
                color: #2c3e50;
            }
            
            .gantt_grid_data .gantt_cell {
                border-right: 1px solid #eee;
            }
            
            .gantt_task_cell.weekend {
                background-color: #f8f9fa;
            }
            
            .gantt_task_row.gantt_selected {
                background-color: #e8f5fe;
            }
            
            .gantt_task_line.gantt_selected {
                box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
            }
        </style>
        @include('admin::components.gantt.styles.styles')
    @endPushOnce
</x-admin::layouts>