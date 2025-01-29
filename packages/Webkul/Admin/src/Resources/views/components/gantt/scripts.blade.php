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

                // Configuración adicional
                this.configureGanttTemplates();
                this.configureGanttColumns();

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

            configureGanttTemplates() {
                gantt.templates.task_row_class = (start, end, task) => {
                    return this.selectedTask && this.selectedTask.id === task.id ? 'selected-row' : '';
                };

                gantt.templates.tooltip_text = function(start, end, task) {
                    return `<b>Tarea:</b> ${task.text}<br/>
                            <b>Inicio:</b> ${gantt.templates.tooltip_date_format(start)}<br/>
                            <b>Duración:</b> ${task.duration} días<br/>
                            <b>Progreso:</b> ${Math.round(task.progress * 100)}%`;
                };
            },

            configureGanttColumns() {
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
                    const newTask = {
                        ...taskData,
                        id: Date.now(),
                        duration: this.calculateDuration(taskData.start_date, taskData.end_date)
                    };
                    this.ganttInstance.addTask(newTask);
                } else {
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
                }
            }
        }
    });
</script>