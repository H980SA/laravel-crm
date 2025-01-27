<v-leads-views></v-leads-views>
@push('scripts')

    <script type="text/x-template" id="v-leads-views-template">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h4 class="mb-4 text-lg font-bold dark:text-white">
                @lang('admin::app.leads.view.metrics.title')
            </h4>
    
            <template v-if="metrics">
        
                <!-- Mostrar métricas -->
                <div class="space-y-4" v-if="!isEditing">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.etapa_licitacion')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.etapa_licitacion }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.capacidad_financiera')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.capacidad_financiera }}</span>
                    </div>
    
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.capacidad_tecnica')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.capacidad_tecnica }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.inteligencia_precios')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.inteligencia_precios }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.experiencia_servicios')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.experiencia_servicios }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.reputacion_mur')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.reputacion_mur }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.conocimiento_costos')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.conocimiento_costos }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leds.view.metrics.cumplimiento_norma')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.cumplimiento_norma }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.relacion_cliente')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.relacion_cliente }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.innovacion')
                        </span>
                        <span class="font-semibold dark:text-white">@{{ metrics.innovacion }}</span>
                    </div>
    
                    <div class="mt-4">
                        <button
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                            @click="editMetrics"
                        >
                            @lang('admin::app.leads.view.metrics.edit_metrics')
                        </button>
                    </div>
                </div>
    
                <!-- Formulario para editar métricas -->
                <form v-else @submit.prevent="updateMetrics">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.etapa_licitacion')
                        </label>
                        <select v-model="metrics.etapa_licitacion" 
                                class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" 
                                required>
                            <option value=1>El cliente evalúa propuesta</option>
                            <option value=2>Short List</option>
                            <option value=3>Adjudicado</option>
                            <option value=4>El cliente declara en STAND BY</option>
                            <option value=5>El cliente declara en DESIERTO</option>
                            <option value=6>MUR retira la oferta</option>
                            <option value=7>MUR no presenta oferta</option>
                            <option value=8>NO Adjudicado</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.capacidad_financiera')
                        </label>
                        <input type="number" v-model="metrics.capacidad_financiera" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>
    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.capacidad_tecnica')
                        </label>
                        <input type="number" v-model="metrics.capacidad_tecnica" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.inteligencia_precios')
                        </label>
                        <input type="number" v-model="metrics.inteligencia_precios" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.experiencia_servicios')
                        </label>
                        <input type="number" v-model="metrics.experiencia_servicios" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.reputacion_mur')
                        </label>
                        <input type="number" v-model="metrics.reputacion_mur" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.conocimiento_costos')
                        </label>
                        <input type="number" v-model="metrics.conocimiento_costos" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.cumplimiento_norma')
                        </label>
                        <input type="number" v-model="metrics.cumplimiento_norma" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.relacion_cliente')
                        </label>
                        <input type="number" v-model="metrics.relacion_cliente" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.innovacion')
                        </label>
                        <input type="number" v-model="metrics.innovacion" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>
    
                    <div class="flex gap-4">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            @lang('admin::app.leads.view.metrics.update_metrics')
                        </button>
                        <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
                            @click="cancelEdit">
                            @lang('admin::app.leads.view.metrics.cancel')
                        </button>
                    </div>
                </form>
            </template>
    
            <!-- Formulario para crear métricas -->
            <template v-else>
                <form @submit.prevent="createMetrics">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.etapa_licitacion')
                        </label>
                        <select v-model="newMetrics.etapa_licitacion" 
                                class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" 
                                required>
                            <option value="" disabled selected>@lang('admin::app.leads.view.metrics.select_etapa')</option>
                            <option value=1>El cliente evalúa propuesta</option>
                            <option value=2>Short List</option>
                            <option value=3>Adjudicado</option>
                            <option value=4>El cliente declara en STAND BY</option>
                            <option value=5>El cliente declara en DESIERTO</option>
                            <option value=6>MUR retira la oferta</option>
                            <option value=7>MUR no presenta oferta</option>
                            <option value=8>NO Adjudicado</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.capacidad_financiera')
                        </label>
                        <input type="number" v-model="newMetrics.capacidad_financiera" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>
    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.capacidad_tecnica')
                        </label>
                        <input type="number" v-model="newMetrics.capacidad_tecnica" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.inteligencia_precios')
                        </label>
                        <input type="number" v-model="newMetrics.inteligencia_precios" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.experiencia_servicios')
                        </label>
                        <input type="number" v-model="newMetrics.experiencia_servicios" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.reputacion_mur')
                        </label>
                        <input type="number" v-model="newMetrics.reputacion_mur" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.conocimiento_costos')
                        </label>
                        <input type="number" v-model="newMetrics.conocimiento_costos" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.cumplimiento_norma')
                        </label>
                        <input type="number" v-model="newMetrics.cumplimiento_norma" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.relacion_cliente')
                        </label>
                        <input type="number" v-model="newMetrics.relacion_cliente" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('admin::app.leads.view.metrics.innovacion')
                        </label>
                        <input type="number" v-model="newMetrics.innovacion" min="0" max="10"
                            class="w-full border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" required />
                    </div>
    
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        @lang('admin::app.leads.view.metrics.create_metrics')
                    </button>
                </form>
            </template>
        </div>
        
        <div class="flex gap-4 mt-4">
            <x-admin::charts.probability-chart 
                ::value="metrics ? metrics.probabilidad_exito : 0"
            ></x-admin::charts.probability-chart>
        </div>
        
    </script>
    
    <script type="module">
        app.component('v-leads-views', {
            template: '#v-leads-views-template',
            data() {
                return {
                    isEditing: false,
                    metrics: @json($lead->metrics ?? null),
                    newMetrics: {
                        capacidad_financiera: '',
                        capacidad_tecnica: '',
                        inteligencia_precios: '',
                        experiencia_servicios: '',
                        reputacion_mur:'',
                        conocimiento_costos:'',
                        cumplimiento_norma:'',
                        relacion_cliente:'',
                        innovacion:''
                    },
                };
            },
            methods: {
                editMetrics() {
                    this.isEditing = true;
                },
                cancelEdit() {
                    this.metrics = @json($lead->metrics ?? null);
                    this.isEditing = false;
                    
                },
                async updateMetrics() {
                    try {
                        const response = await axios.put(`{{ route('admin.leads.metrics.update', $lead->id) }}`, this.metrics);
                        this.metrics= response.data.data;
                        console.log(response.data.data);
                        this.isEditing = false;
                    } catch (error) {
                        alert('Failed to update metrics.');
                    }
                },
                async createMetrics() {
                    try {
                        const response = await axios.post(`{{ route('admin.leads.metrics.store', $lead->id) }}`, this.newMetrics);
                        this.isEditing = false;
                        this.metrics = response.data.data[0];
                        console.log(this.metrics)
                        this.newMetrics = {
                            capacidad_financiera: '',
                            capacidad_tecnica: '',
                            inteligencia_precios: '',
                            experiencia_servicios: '',
                            reputacion_mur:'',
                            conocimiento_costos:'',
                            cumplimiento_norma:'',
                            relacion_cliente:'',
                            innovacion:''
                        };
                        
                    } catch (error) {
                        alert(error.response.data.message);
                    }
                },
            },
        });
    </script>
@endPush