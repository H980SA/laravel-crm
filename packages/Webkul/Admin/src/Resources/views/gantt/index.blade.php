<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.gantt.index.title')
    </x-slot>

    <gantt-chart></gantt-chart>

    @pushOnce('scripts')
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
        <script type="text/javascript">
            // Definir el componente
            const ganttApp = {
                template: `
                    <div class="flex gap-4 max-w-full">
                        <!-- Gantt Section -->
                        <div class="flex-1">
                            <div class="flex flex-col gap-4">
                                <!-- Header Section -->
                                @include('admin::components.gantt.header')

                                <!-- Gantt Component -->
                                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm">
                                    <!-- Scale Controls -->
                                    @include('admin::components.gantt.scale-controls')
                                    
                                    <!-- Gantt Container -->
                                    <div class="relative">
                                        <div id="gantt_here" style="width:100%; height:600px; overflow: hidden;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        @include('admin::components.gantt.sidebar')
                    </div>
                `,
                
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
                    console.log('Componente montado');
                    this.initGantt();
                    this.loadLicitaciones();
                    this.updateTodayTasks();
                },

                methods: {
                    startNewTask() {
                        console.log("Iniciando nueva tarea");
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
                        console.log('Estado después de startNewTask:', { isCreatingTask: this.isCreatingTask, currentTask: this.currentTask });
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

                    initGantt() {
                        gantt.config.xml_date = "%Y-%m-%d %H:%i";
                        gantt.config.date_format = "%Y-%m-%d %H:%i";
                        gantt.config.scale_height = 60;
                        gantt.config.row_height = 35;
                        gantt.config.task_height = 20;
                        gantt.config.grid_width = 380;
                        gantt.config.autosize = "y";
                        
                        gantt.config.columns = [
                            {name: "text", label: "Tarea", tree: true, width: '*', min_width: 200},
                            {name: "start_date", label: "Inicio", align: "center", width: 90},
                            {name: "duration", label: "Duración", align: "center", width: 60},
                            {name: "priority", label: "Prioridad", align: "center", width: 80}
                        ];
                        
                        gantt.init("gantt_here");
                        this.loadData();

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
                                this.updateTodayTasks();
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

                    updateTodayTasks() {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        this.todayTasks = this.tasks.filter(task => {
                            const taskDate = new Date(task.start_date);
                            taskDate.setHours(0, 0, 0, 0);
                            return taskDate.getTime() === today.getTime();
                        });
                    },

                    formatDate(dateStr) {
                        return new Date(dateStr).toLocaleDateString();
                    }
                }
            };

            // Registrar el componente en la aplicación principal
            window.addEventListener("load", function() {
                app.component('gantt-chart', ganttApp);
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
        @include('admin::components.gantt.styles.gantt')
    @endPushOnce
</x-admin::layouts>