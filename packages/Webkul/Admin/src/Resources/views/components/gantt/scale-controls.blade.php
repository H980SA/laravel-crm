<div class="flex items-center gap-4 p-4 border-b border-gray-200 dark:border-gray-700">
    <button 
        v-for="item in [{value: 'day', label: 'Día'}, {value: 'week', label: 'Semana'}, {value: 'month', label: 'Mes'}]"
        :key="item.value"
        type="button"
        class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
        :class="{'bg-blue-600 text-white': scale === item.value, 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200': scale !== item.value}"
        @click="setScale(item.value)"
    >
        @{{ item.label }}
    </button>
</div>