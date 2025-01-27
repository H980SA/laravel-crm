<probability-chart {{ $attributes }}></probability-chart>

@pushOnce('scripts')
    <script type="text/x-template" id="probability-chart-template">
        <div 
            class="flex w-[400px] h-[400px] items-center justify-center"
            @mouseenter="isHovered = true"
            @mouseleave="isHovered = false"
        >
            <canvas 
                :id="$.uid + '_chart'"
                class="w-full h-full"
            ></canvas>
        </div>
    </script>

    <!-- Cargar Chart.js desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script type="module">
        app.component('probability-chart', {
            template: '#probability-chart-template',

            props: {
                value: {
                    type: Number,
                    default: 0,
                }
            },

            data() {
                return {
                    chart: null,
                    isHovered: false,
                }
            },

            watch: {
                isHovered() {
                    if (this.chart) {
                        this.chart.update();
                    }
                },
                value() {
                    this.drawGauge();
                },

                value(newVal) {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    this.drawGauge();
                }
            },

            mounted() {
                this.drawGauge();
            },

            methods: {
                drawGauge() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const baseColor = this.getInterpolatedColor(this.value);
                    const hoverColor = this.getHoveredColor(this.value);

                    // Usar Chart desde el objeto global (CDN)
                    this.chart = new window.Chart(
                        document.getElementById(this.$.uid + '_chart'),
                        {
                            type: 'doughnut',
                            data: {
                                datasets: [{
                                    data: [this.value, 1 - this.value],
                                    backgroundColor: [
                                        baseColor,
                                        '#E0E0E0',
                                    ],
                                    hoverBackgroundColor: [
                                        hoverColor,
                                        '#E0E0E0',
                                    ],
                                    borderColor: ['#FFFFFF', '#FFFFFF'],
                                    borderWidth: 2,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                                hover: {
                                    mode: 'index',
                                    intersect: true,
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { enabled: false },
                                },
                            },
                            plugins: [{
                                id: 'center-text',
                                afterDraw: (chart) => {
                                    const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                                    
                                    ctx.save();
                                    ctx.font = 'bold 28px sans-serif';
                                    ctx.fillStyle = this.isHovered 
                                        ? this.getHoveredColor(this.value)
                                        : '#FFFFFF';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    
                                    ctx.fillText(
                                        (this.value * 100).toFixed(1) + '%',
                                        left + width / 2,
                                        top + height / 2
                                    );
                                    
                                    ctx.restore();
                                },
                            }],
                        }
                    );
                },

                calculateRGB(value) {
                    const startColor = [255, 87, 51];
                    const endColor = [60, 218, 60];
                    return [
                        Math.round(startColor[0] * (1 - value) + endColor[0] * value),
                        Math.round(startColor[1] * (1 - value) + endColor[1] * value),
                        Math.round(startColor[2] * (1 - value) + endColor[2] * value)
                    ];
                },

                getInterpolatedColor(value) {
                    const [r, g, b] = this.calculateRGB(value);
                    return `rgba(${r}, ${g}, ${b}, 0.7)`;
                },

                getHoveredColor(value) {
                    const [r, g, b] = this.calculateRGB(value);
                    return `rgb(${r}, ${g}, ${b})`;
                },
            },
        });
    </script>
@endPushOnce