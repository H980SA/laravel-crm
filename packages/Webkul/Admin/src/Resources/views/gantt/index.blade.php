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
                        
                        // Configurar la altura total del encabezado
                        gantt.config.scale_height = 60;

                        switch (value) {
                            case 'day':
                                gantt.config.scales = [
                                    {
                                        unit: "month", 
                                        step: 1, 
                                        format: "%F, %Y",
                                        css: function(date) {
                                            return "gantt_scale_month";
                                        }
                                    },
                                    {
                                        unit: "day", 
                                        step: 1, 
                                        format: "%j %D",
                                        css: function(date) {
                                            return "gantt_scale_day";
                                        }
                                    }
                                ];
                                gantt.config.min_column_width = 50;
                                break;
                                
                            case 'week':
                                gantt.config.scales = [
                                    {
                                        unit: "month", 
                                        step: 1, 
                                        format: "%F, %Y",
                                        height: 30
                                    },
                                    {
                                        unit: "week", 
                                        step: 1, 
                                        format: function(date) {
                                            var weekNum = gantt.date.date_to_str("%W")(date);
                                            return "Semana " + weekNum;
                                        },
                                        height: 30
                                    }
                                ];
                                gantt.config.min_column_width = 70;
                                break;
                                
                            case 'month':
                                gantt.config.scales = [
                                    {
                                        unit: "year", 
                                        step: 1, 
                                        format: "%Y",
                                        height: 30
                                    },
                                    {
                                        unit: "month", 
                                        step: 1, 
                                        format: "%F",
                                        height: 30
                                    }
                                ];
                                gantt.config.min_column_width = 100;
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
                        gantt.config.grid_width = 400;
                        gantt.config.autosize = "y";
                        
                        // Personalizar el estilo de las escalas
                        gantt.templates.scale_cell_class = function(date) {
                            return "gantt_scale_cell";
                        };
                        
                        gantt.templates.scale_row_class = function(scale) {
                            return "gantt_scale_row";
                        };

                        // Configuración inicial de la escala
                        this.setScale(this.scale);
                        
                        gantt.config.columns = [
                            {name: "text", label: "Tarea", tree: true, width: 180, min_width: 150},
                            {
                                name: "start_date", 
                                label: "Inicio", 
                                align: "center", 
                                width: 120,
                                template: function(task) {
                                    return gantt.date.date_to_str("%d/%m/%Y")(task.start_date);
                                }
                            },
                            {
                                name: "end_date", 
                                label: "Fin", 
                                align: "center", 
                                width: 120,
                                template: function(task) {
                                    return gantt.date.date_to_str("%d/%m/%Y")(task.end_date);
                                }
                            },
                            {name: "duration", label: "Duración", align: "center", width: 70},
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
                        
                        // Forzar un rerender después de la inicialización
                        setTimeout(() => {
                            gantt.render();
                        }, 100);
                        
                        this.loadData();

                        // Eventos para sincronización bidireccional
                        gantt.attachEvent("onTaskClick", (id) => {
                            this.selectTask(id);
                            return true;
                        });

                        // Mantener los últimos valores válidos
                        let lastValidValues = {
                            text: '',
                            start_date: null,
                            end_date: null,
                            progress: 0,
                            priority: 'Media'
                        };

                        // Durante el arrastre de la tarea (solo afecta fechas)
                        gantt.attachEvent("onTaskDrag", (id, mode, task, original) => {
                            if (this.selectedTask && this.selectedTask.id === id) {
                                // Solo actualizar fechas durante el arrastre
                                this.updateSidebarTask({
                                    start_date: task.start_date,
                                    end_date: task.end_date
                                });
                            }
                        });

                        // Durante el arrastre del progreso (solo afecta progreso)
                        gantt.attachEvent("onTaskProgressDrag", (id, progress, task) => {
                            if (this.selectedTask && this.selectedTask.id === id) {
                                // Evitar que el progreso afecte otras propiedades
                                const validProgress = Math.min(Math.max(progress, 0), 1);
                                
                                // Actualizar solo el progreso sin tocar otras propiedades
                                this.currentTask = {
                                    ...this.currentTask,
                                    progress: Math.round(validProgress * 100)
                                };

                                // Actualizar el progreso en el gantt sin modificar fechas
                                const ganttTask = gantt.getTask(id);
                                ganttTask.progress = validProgress;
                                gantt.updateTask(id);
                            }
                        });

                        // Después de soltar el progreso
                        gantt.attachEvent("onAfterTaskUpdate", (id, task) => {
                            if (this.selectedTask && this.selectedTask.id === id) {
                                // Preservar las fechas originales
                                const originalStartDate = this.currentTask.start_date;
                                const originalEndDate = this.currentTask.end_date;
                                
                                // Actualizar solo si el progreso ha cambiado
                                if (typeof task.progress === 'number' && !isNaN(task.progress)) {
                                    this.currentTask = {
                                        ...this.currentTask,
                                        progress: Math.round(task.progress * 100),
                                        start_date: originalStartDate,
                                        end_date: originalEndDate
                                    };
                                }
                            }
                        });

                        // Actualizar en cualquier cambio de la tarea
                        gantt.attachEvent("onTaskChanged", (id, task) => {
                            if (this.selectedTask && this.selectedTask.id === id) {
                                this.updateSidebarTask(task);
                            }
                        });

                        // Configurar la visualización de la barra de tarea
                        gantt.templates.task_cell_class = function(task, date) {
                            return "gantt_task_cell";
                        };

                        // Personalizar el renderizado de la barra de tarea
                        gantt.templates.task_text = function(start, end, task) {
                            return `<div class="task-content">
                                        <div class="task-title">${task.text}</div>
                                        <div class="task-dates">${gantt.date.date_to_str("%d %M")(start)} - ${gantt.date.date_to_str("%d %M")(end)}</div>
                                    </div>`;
                        };
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
                        this.isCreatingTask = false;
                        
                        // Actualizar el currentTask con todos los datos necesarios
                        this.currentTask = {
                            text: task.text,
                            start_date: this.formatDateForInput(task.start_date),
                            end_date: this.formatDateForInput(task.end_date),
                            progress: Math.round(task.progress * 100),
                            priority: task.priority || 'Media',
                            parent: task.parent || '',
                            type: task.type || 'task'
                        };
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
                    },

                    // Método para actualizar el Gantt cuando cambia el sidebar
                    updateGanttTask() {
                        if (this.selectedTask) {
                            const taskId = this.selectedTask.id;
                            const task = gantt.getTask(taskId);
                            const updates = {};

                            // Manejar progreso de forma independiente
                            if (typeof this.currentTask.progress === 'number') {
                                const newProgress = Math.min(Math.max(this.currentTask.progress, 0), 100) / 100;
                                if (newProgress !== task.progress) {
                                    updates.progress = newProgress;
                                }
                            }

                            // Manejar otras propiedades solo si no estamos actualizando el progreso
                            if (!('progress' in updates)) {
                                if (this.currentTask.text !== task.text) {
                                    updates.text = this.currentTask.text;
                                }

                                const currentStartDate = new Date(this.currentTask.start_date);
                                const currentEndDate = new Date(this.currentTask.end_date);
                                
                                if (currentStartDate.getTime() !== task.start_date.getTime()) {
                                    updates.start_date = currentStartDate;
                                }
                                
                                if (currentEndDate.getTime() !== task.end_date.getTime()) {
                                    updates.end_date = currentEndDate;
                                }

                                if (this.currentTask.priority !== task.priority) {
                                    updates.priority = this.currentTask.priority;
                                }
                            }

                            // Aplicar actualizaciones
                            if (Object.keys(updates).length > 0) {
                                Object.assign(task, updates);
                                gantt.updateTask(taskId);
                                gantt.render();
                            }
                        }
                    },

                    // Método mejorado para actualizar el sidebar
                    updateSidebarTask(task) {
                        this.$nextTick(() => {
                            const currentTask = { ...this.currentTask };
                            
                            // Manejar progreso de forma independiente
                            if (typeof task.progress === 'number' && !isNaN(task.progress)) {
                                currentTask.progress = Math.round(task.progress * 100);
                            }

                            // Mantener otras propiedades solo si están presentes en la actualización
                            if (task.text) currentTask.text = task.text;
                            if (task.start_date) currentTask.start_date = this.formatDateForInput(task.start_date);
                            if (task.end_date) currentTask.end_date = this.formatDateForInput(task.end_date);
                            if (task.priority) currentTask.priority = task.priority;

                            this.currentTask = currentTask;
                        });
                    },

                    // Watch para cambios en currentTask
                    watch: {
                        'currentTask.text'(newVal) {
                            this.updateGanttTask();
                        },
                        'currentTask.start_date'(newVal) {
                            this.updateGanttTask();
                        },
                        'currentTask.end_date'(newVal) {
                            this.updateGanttTask();
                        },
                        'currentTask.progress'(newVal) {
                            this.updateGanttTask();
                        },
                        'currentTask.priority'(newVal) {
                            this.updateGanttTask();
                        }
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
        <style>
            .gantt_container {
                width: 100% !important;
                min-width: 900px !important;
                overflow-x: auto !important;
            }
            
            .gantt_grid {
                width: auto !important;
            }
            
            .gantt_task {
                width: auto !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            /* Mejorar la visualización de las fechas */
            .gantt_grid_data .gantt_cell {
                padding: 0 10px !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Ajustar el espaciado de las columnas */
            .gantt_grid_scale .gantt_grid_head_cell {
                padding: 0 10px !important;
                font-weight: 600;
            }

            /* Mejorar la visualización de la barra de tareas */
            .gantt_task_line {
                border-radius: 3px;
            }

            /* Ajustar el tamaño del texto en las barras */
            .gantt_task_content {
                font-size: 12px;
                padding: 0 6px;
            }
        </style>
        @include('admin::components.gantt.styles.gantt')
    @endPushOnce
</x-admin::layouts>