// Initialize Vue instance for Gantt
new Vue({
    el: "#gantt-app",

    data: {
        testMessage: "Vue is working",
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
        },
        licitaciones: [], // Will store project-type tasks
        todayTasks: [],
    },

    mounted() {
        this.initGantt();
        this.loadLicitaciones();
        this.updateTodayTasks();
    },

    methods: {
        initGantt() {
            gantt.init("gantt_here");

            // Configure Gantt settings
            gantt.config.date_format = "%Y-%m-%d %H:%i";
            gantt.config.xml_date = "%Y-%m-%d %H:%i";

            // Add task click handler
            gantt.attachEvent("onTaskClick", (id) => {
                this.selectTask(id);
                return true;
            });

            // Load data
            this.loadData();
        },

        async loadData() {
            try {
                this.isLoading = true;
                const response = await fetch("/api/admin/gantt/data");
                const data = await response.json();

                this.tasks = data.tasks || [];
                this.links = data.links || [];

                gantt.parse({
                    data: this.tasks,
                    links: this.links,
                });

                this.updateTodayTasks();
            } catch (error) {
                console.error("Error loading Gantt data:", error);
            } finally {
                this.isLoading = false;
            }
        },

        async loadLicitaciones() {
            try {
                const response = await fetch("/api/admin/gantt/licitaciones");
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
            console.log("startNewTask called");
            this.isCreatingTask = true;
            this.selectedTask = null;
            this.currentTask = {
                text: "",
                start_date: new Date().toISOString().split("T")[0],
                end_date: new Date().toISOString().split("T")[0],
                progress: 0,
                parent: "",
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
            };
        },

        async saveTask() {
            try {
                const taskData = {
                    ...this.currentTask,
                    progress: this.currentTask.progress / 100, // Convert to decimal
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

                // Reload data to refresh the gantt chart
                await this.loadData();
                this.closeTaskForm();
            } catch (error) {
                console.error("Error saving task:", error);
                // You might want to show an error message to the user here
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
    },
});
