<style>
    [v-cloak] { display: none; }
    
    .gantt_container {
        font-family: var(--font-family);
        font-size: var(--font-size-base);
        border-radius: 0.5rem;
    }

    .gantt_grid_scale, 
    .gantt_task_scale {
        background-color: #f8fafc;
        color: #64748b;
    }

    .gantt_scale_cell {
        font-weight: 500;
    }

    .dark .gantt_grid_scale,
    .dark .gantt_task_scale {
        background-color: #1e293b;
        color: #94a3b8;
    }

    .gantt_task_progress {
        background-color: #3b82f6;
    }

    .gantt_task_line {
        background-color: #60a5fa;
        border-color: #3b82f6;
    }

    .gantt_task_line.gantt_project {
        background-color: #f59e0b;
        border-color: #d97706;
    }

    .dark .gantt_task_line {
        background-color: #3b82f6;
        border-color: #2563eb;
    }

    .dark .gantt_task_line.gantt_project {
        background-color: #d97706;
        border-color: #b45309;
    }

    .selected-row {
        background-color: rgba(59, 130, 246, 0.1);
    }
</style>