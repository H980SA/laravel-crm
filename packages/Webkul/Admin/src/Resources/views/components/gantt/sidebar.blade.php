<div class="w-96 flex-shrink-0">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-4">
        <div v-if="selectedTask || isCreatingTask" class="task-form">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">@{{ isCreatingTask ? 'Nueva Tarea' : 'Editar Tarea' }}</h3>
                <button @click="closeTaskForm" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                    <input 
                        type="text" 
                        v-model="currentTask.text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div v-if="isCreatingTask">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Licitación</label>
                    <select 
                        v-model="currentTask.parent"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Seleccione una licitación</option>
                        <option v-for="lic in licitaciones" :key="lic.id" :value="lic.id">
                            @{{ lic.text }}
                        </option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Inicio</label>
                        <input 
                            type="date" 
                            v-model="currentTask.start_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Fin</label>
                        <input 
                            type="date" 
                            v-model="currentTask.end_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Progreso (%)</label>
                    <input 
                        type="number" 
                        v-model.number="currentTask.progress"
                        min="0"
                        max="100"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div class="flex justify-end">
                    <button 
                        @click="saveTask"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        @{{ isCreatingTask ? 'Crear Tarea' : 'Actualizar Tarea' }}
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="text-center py-8">
            <h3 class="text-lg font-medium mb-2">Gestión de Tareas</h3>
            <p class="text-gray-500 mb-4">Selecciona una tarea para editar o crea una nueva</p>
            <button 
                type="button"
                @click="startNewTask"
                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            >
                + Nueva Tarea
            </button>
        </div>

        <!-- Today's Tasks -->
        <div class="mt-8">
            <h3 class="text-lg font-medium mb-4">Tareas de Hoy</h3>
            <div v-if="todayTasks && todayTasks.length" class="space-y-4">
                <div 
                    v-for="task in todayTasks" 
                    :key="task.id"
                    class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                >
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium">@{{ task.text }}</h4>
                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                            @{{ task.type === 'project' ? 'Licitación' : 'Tarea' }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <div>Inicio: @{{ formatDate(task.start_date) }}</div>
                        <div>Fin: @{{ formatDate(task.end_date) }}</div>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div 
                                class="bg-blue-600 h-2.5 rounded-full" 
                                :style="{ width: (task.progress * 100) + '%' }"
                            ></div>
                        </div>
                        <div class="text-right text-xs mt-1">@{{ Math.round(task.progress * 100) }}%</div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-4 text-gray-500">
                No hay tareas programadas para hoy
            </div>
        </div>
    </div>
</div>