<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.gantt.index.title')
    </x-slot>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                            type: "task",
                            priority: "Media"
                        },
                        licitaciones: [],
                        todayTasks: [],
                        scale: 'week',
                        isSaving: false
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
                            type: "task",
                            priority: "Media"
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
                            if (this.isSaving) return;
                            this.isSaving = true;
                            
                            // Validaciones básicas
                            if (!this.currentTask.text) {
                                throw new Error("El nombre de la tarea es requerido");
                            }
                            if (!this.currentTask.start_date || !this.currentTask.end_date) {
                                throw new Error("Las fechas son requeridas");
                            }
                            if (this.currentTask.progress < 0 || this.currentTask.progress > 100) {
                                throw new Error("El progreso debe estar entre 0 y 100");
                            }

                            const taskData = {
                                text: this.currentTask.text,
                                start_date: this.currentTask.start_date,
                                end_date: this.currentTask.end_date,
                                progress: this.currentTask.progress / 100,
                                type: this.isCreatingTask ? 'task' : (this.selectedTask.type || 'task'),
                                parent: this.currentTask.parent || null,
                                priority: this.currentTask.priority || 'Media'
                            };

                            const url = this.isCreatingTask
                                ? "/api/admin/gantt/tasks"
                                : `/api/admin/gantt/tasks/${this.selectedTask.id}`;

                            const response = await fetch(url, {
                                method: this.isCreatingTask ? "POST" : "PUT",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                    "Accept": "application/json"
                                },
                                credentials: 'same-origin',
                                body: JSON.stringify(taskData)
                            });

                            const result = await response.json();

                            if (!response.ok) {
                                throw new Error(result.message || "Error al guardar la tarea");
                            }

                            if (result.success) {
                                await this.loadData();
                                this.closeTaskForm();
                                
                                if (typeof this.$parent.$root.$refs !== 'undefined' && 
                                    typeof this.$parent.$root.$refs.notifications !== 'undefined') {
                                    this.$parent.$root.$refs.notifications.success(
                                        this.isCreatingTask ? 'Tarea creada exitosamente' : 'Tarea actualizada exitosamente'
                                    );
                                }
                            }
                        } catch (error) {
                            console.error("Error saving task:", error);
                            if (typeof this.$parent.$root.$refs !== 'undefined' && 
                                typeof this.$parent.$root.$refs.notifications !== 'undefined') {
                                this.$parent.$root.$refs.notifications.error(
                                    error.message || "Error al guardar la tarea"
                                );
                            }
                        } finally {
                            this.isSaving = false;
                        }
                    },

                    setScale(value) {
                        this.scale = value;
                        
                        switch (value) {
                            case 'day':
                                gantt.config.scale_unit = 'month';
                                gantt.config.date_scale = '%F, %Y';
                                gantt.config.subscales = [
                                    {unit: 'day', step: 1, date: '%d %M'}
                                ];
                                gantt.config.min_column_width = 80;
                                break;
                                
                            case 'week':
                                gantt.config.scale_unit = 'month';
                                gantt.config.date_scale = '%F, %Y';
                                gantt.config.subscales = [
                                    {unit: 'week', step: 1, date: 'Semana %W'}
                                ];
                                gantt.config.min_column_width = 50;
                                break;
                                
                            case 'month':
                                gantt.config.scale_unit = 'year';
                                gantt.config.date_scale = '%Y';
                                gantt.config.subscales = [
                                    {unit: 'month', step: 1, date: '%F'}
                                ];
                                gantt.config.min_column_width = 120;
                                break;
                        }
                        
                        gantt.render();
                    },

                    initGantt() {
                        gantt.config.xml_date = "%Y-%m-%d %H:%i";
                        gantt.config.date_format = "%Y-%m-%d %H:%i";
                        gantt.config.scale_height = 60;
                        gantt.config.row_height = 35;
                        gantt.config.task_height = 20;
                        gantt.config.grid_width = 380;
                        gantt.config.autosize = "y";
                        
                        // Configuración inicial de la escala (semana por defecto)
                        this.setScale(this.scale);
                        
                        gantt.config.columns = [
                            {name: "text", label: "Tarea", tree: true, width: '*', min_width: 200},
                            {name: "start_date", label: "Inicio", align: "center", width: 90},
                            {name: "end_date", label: "Fin", align: "center", width: 90},
                            {name: "duration", label: "Duración", align: "center", width: 60},
                            {name: "priority", label: "Prioridad", align: "center", width: 80},
                            {
                                name: "progress", 
                                label: "Progreso", 
                                align: "center", 
                                width: 80,
                                template: function(task) {
                                    return Math.round(task.progress * 100) + "%";
                                }
                            }
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
                        const task = gantt.getTask(taskId);
                        this.selectedTask = task;
                        
                        // Formatear las fechas y datos para el formulario
                        this.currentTask = {
                            ...task,
                            start_date: this.formatDateForInput(task.start_date),
                            end_date: this.formatDateForInput(task.end_date),
                            progress: Math.round(task.progress * 100), // Convertir el progreso a porcentaje
                            priority: task.priority || 'Media' // Asegurar que siempre haya una prioridad
                        };
                        
                        this.isCreatingTask = false;
                    },

                    // Agregar este método helper para formatear las fechas
                    formatDateForInput(date) {
                        const d = new Date(date);
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
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