<style>
/* Estilos del Gantt */
.gantt_task_line {
    border-radius: 6px;
    height: 24px !important;
    line-height: 24px !important;
    background-color: rgb(14 144 217);
    border-color: rgb(12 124 187);
}

.gantt_task_line.gantt_project {
    background-color: rgb(11 114 173);
    border-color: rgb(10 104 158);
    border-radius: 4px;
    height: 28px !important;
    line-height: 28px !important;
}

.gantt_task_line.weekend {
    background-color: rgba(14, 144, 217, 0.1);
    border-color: rgba(14, 144, 217, 0.2);
}

.gantt_grid_scale,
.gantt_task_scale {
    background-color: #f9fafb;
    color: #1f2937;
    font-weight: 500;
}

.gantt_grid_data .gantt_cell {
    border-right: 1px solid #e5e7eb;
    color: #374151;
}

.gantt_task_cell.weekend {
    background-color: #f3f4f6;
}

.gantt_task_row.gantt_selected {
    background-color: rgba(14, 144, 217, 0.1);
}

.gantt_task_line.gantt_selected {
    box-shadow: 0 2px 8px rgba(14, 144, 217, 0.3);
}

/* Estilos del Formulario */
.task-form {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.task-form h3 {
    color: #1a202c;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.task-form label {
    color: #4a5568;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.task-form input,
.task-form select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background-color: white;
    transition: all 0.2s;
}

.task-form input:focus,
.task-form select:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
    outline: none;
}

.task-form .button-group {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.task-form button {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s;
}

.task-form button.save {
    background-color: rgb(14 144 217);
    color: white;
    border: none;
}

.task-form button.save:hover {
    opacity: 0.9;
}

.task-form button.cancel {
    background-color: #f3f4f6;
    color: #4b5563;
    border: 1px solid #d1d5db;
}

.task-form button.cancel:hover {
    background-color: #e5e7eb;
}

/* Estilos para las Tareas de Hoy */
.today-tasks {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.today-tasks h3 {
    color: #1a202c;
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.task-card {
    background-color: #f9fafb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    border: 1px solid #e5e7eb;
}

.task-card:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.task-card .task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.task-card .task-title {
    font-weight: 500;
    color: #1a202c;
}

.task-card .task-type {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    background-color: #e3f2fd;
    color: #1976d2;
}

.task-card .task-dates {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
}

.task-card .progress-bar {
    height: 6px;
    background-color: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
}

.task-card .progress-value {
    height: 100%;
    background-color: rgb(14 144 217);
    border-radius: 9999px;
    transition: width 0.3s ease;
}

/* Botón Nueva Tarea */
.new-task-button {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    background-color: rgb(14 144 217);
    color: white;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.new-task-button:hover {
    opacity: 0.9;
}

.new-task-button:active {
    transform: translateY(0);
}

.new-task-button svg {
    margin-right: 0.5rem;
    width: 1.25rem;
    height: 1.25rem;
}

/* Estilos de los botones */
.button-group {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.button-group button {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

.button-group button.cancel {
    background-color: #f3f4f6;
    color: #374151;
}

.button-group button.save {
    background-color: #2563eb;
    color: white;
}

.button-group button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.button-group button:not(:disabled).cancel:hover {
    background-color: #e5e7eb;
}

.button-group button:not(:disabled).save:hover {
    background-color: #1d4ed8;
}

/* Animación de loading */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style> 