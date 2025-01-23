{!! view_render_event('admin.dashboard.index.total_gantts.before') !!}

<!-- Total Gantts Vue Component -->
<v-dashboard-total-gantts>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.index.total-gantts />
</v-dashboard-total-gantts>

{!! view_render_event('admin.dashboard.index.total_gantts.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-total-gantts-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.index.total-gantts />
        </template>

        <!-- Pie Chart Section -->
        <template v-else>
            <div class="grid gap-4 rounded-lg border border-gray-200 bg-white px-4 py-2 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col justify-between gap-1">
                    <p class="text-base font-semibold dark:text-gray-300">
                        {{ "Hola Heidi" }} 
                    </p>
                </div>

                <!-- Pie Chart -->
                <div class="flex justify-center">
                    <x-admin::charts.pie
                        ::labels="chartLabels"
                        ::datasets="chartDatasets"
                    />
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-total-gantts', {
            template: '#v-dashboard-total-gantts-template',

            data() {
                return {
                    tasks: [],
                    isLoading: true,
                }
            },

            computed: {
                chartLabels() {
                    // Extract unique types of tasks
                    return [...new Set(this.tasks.map(task => task.type))];
                },

                chartDatasets() {
                    const typeCounts = this.chartLabels.map(type => {
                        // Count the number of tasks for each type
                        return this.tasks.filter(task => task.type === type).length;
                    });

                    return [
                        {
                            data: typeCounts,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'], // Define colors for each type
                        },
                    ];
                }
            },

            mounted() {
                this.fetchTasks();
            },

            methods: {
                fetchTasks() {
                    this.isLoading = true;

                    // Simulated fetch with mock data
                    setTimeout(() => {
                        this.tasks = [
                            { id: 1, name: 'Design Homepage', type: 'Doing' },
                            { id: 2, name: 'Develop API', type: 'In Progress' },
                            { id: 3, name: 'Test Application', type: 'New' },
                            { id: 4, name: 'Fix Bugs', type: 'In Progress' },
                            { id: 5, name: 'Update Documentation', type: 'Doing' },
                            { id: 6, name: 'Client Feedback', type: 'New' },
                            { id: 7, name: 'Deploy App', type: 'Completed' },
                        ];
                        this.isLoading = false;
                    }, 1000); // Simulate a 1-second API delay
                }
            }
        });
    </script>
@endPushOnce
