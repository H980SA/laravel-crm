<v-charts-pie {{ $attributes }}></v-charts-pie>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-charts-pie-template"
    >
        <div class="flex justify-center w-[400px] h-[400px]">
            <canvas
                :id="$.uid + '_chart'"
                class="w-full h-full"
            ></canvas>
        </div>
    </script>

    <script type="module">
        app.component('v-charts-pie', {
            template: '#v-charts-pie-template',

            props: {
                labels: {
                    type: Array,
                    default: [],
                },

                datasets: {
                    type: Array,
                    default: [],
                },

                aspectRatio: {
                    type: Number,
                    default: 1,
                },
            },

            data() {
                return {
                    chart: undefined,
                };
            },

            mounted() {
                this.prepare();
            },

            methods: {
                prepare() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    this.chart = new Chart(document.getElementById(this.$.uid + '_chart'), {
                        type: 'pie',

                        data: {
                            labels: this.labels,

                            datasets: this.datasets,
                        },

                        options: {
                            responsive: true,
                            maintainAspectRatio: false,

                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                            },
                        },
                    });
                },
            },
        });
    </script>
@endPushOnce
