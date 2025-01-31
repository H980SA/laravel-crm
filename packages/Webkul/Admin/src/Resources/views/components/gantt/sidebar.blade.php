<div class="w-96 flex-shrink-0">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-4">
        <div v-if="selectedTask || isCreatingTask" class="task-form">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">@{{ isCreatingTask ? 'Nueva Tarea' : 'Editar Tarea' }}</h3>
                <button @click="closeTaskForm" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Nombre</label>
                    <input 
                        type="text" 
                        v-model="currentTask.text"
                        placeholder="Nombre de la tarea"
                        class="mt-1"
                    >
                </div>

                <div v-if="isCreatingTask">
                    <label class="block text-sm font-medium">Licitación</label>
                    <select 
                        v-model="currentTask.parent"
                        class="mt-1"
                    >
                        <option value="">Seleccione una licitación</option>
                        <option v-for="lic in licitaciones" :key="lic.id" :value="lic.id">
                            @{{ lic.text }}
                        </option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Fecha Inicio</label>
                        <input 
                            type="date" 
                            v-model="currentTask.start_date"
                            class="mt-1"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Fecha Fin</label>
                        <input 
                            type="date" 
                            v-model="currentTask.end_date"
                            class="mt-1"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Progreso (%)</label>
                    <input 
                        type="range" 
                        v-model.number="currentTask.progress"
                        min="0"
                        max="100"
                        step="10"
                        class="mt-1 w-full"
                    >
                    <div class="text-right text-sm text-gray-600 dark:text-gray-400">
                        @{{ currentTask.progress }}%
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Prioridad</label>
                    <select 
                        v-model="currentTask.priority"
                        class="mt-1 w-full"
                    >
                        <option value="Alta">Alta</option>
                        <option value="Media">Media</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>

                <div class="button-group">
                    <button 
                        @click="closeTaskForm"
                        class="cancel"
                        :disabled="isSaving"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="saveTask"
                        class="save"
                        :disabled="isSaving"
                    >
                        <template v-if="isSaving">
                            <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Guardando...
                        </template>
                        <template v-else>
                            @{{ isCreatingTask ? 'Crear Tarea' : 'Actualizar Tarea' }}
                        </template>
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="text-center py-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Gestión de Tareas</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Selecciona una tarea para editar o crea una nueva</p>
            <button 
                type="button"
                @click="startNewTask"
                class="new-task-button"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Tarea
            </button>
        </div>

        <!-- Today's Tasks -->
        <div class="today-tasks">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tareas de Hoy</h3>
            <div v-if="todayTasks && todayTasks.length" class="space-y-4">
                <div 
                    v-for="task in todayTasks" 
                    :key="task.id"
                    class="task-card"
                >
                    <div class="task-header">
                        <h4 class="task-title">@{{ task.text }}</h4>
                        <span class="task-type">
                            @{{ task.type === 'project' ? 'Licitación' : 'Tarea' }}
                        </span>
                    </div>
                    <div class="task-dates">
                        <div>Inicio: @{{ formatDate(task.start_date) }}</div>
                        <div>Fin: @{{ formatDate(task.end_date) }}</div>
                    </div>
                    <div class="progress-bar">
                        <div 
                            class="progress-value" 
                            :style="{ width: (task.progress * 100) + '%' }"
                        ></div>
                    </div>
                    <div class="text-right text-xs mt-1 text-gray-500">
                        @{{ Math.round(task.progress * 100) }}%
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-4 text-gray-500">
                No hay tareas programadas para hoy
            </div>
        </div>
    </div>
</div>