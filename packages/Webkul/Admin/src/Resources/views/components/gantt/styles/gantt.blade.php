<style>
/* Estilos generales del contenedor */
.gantt-container {
    background-color: #ffffff;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.dark .gantt-container {
    background-color: #0f172a;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

/* Estilos del Gantt */
.gantt_task_line {
    border-radius: 6px;
    height: 24px !important;
    line-height: 24px !important;
    background-color: #3b82f6;
    border-color: #2563eb;
    color: #ffffff;
    font-weight: 600;
    font-size: 13px;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}

/* Modo Dark */
.dark .gantt_task_line {
    background-color: #60a5fa;
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.gantt_task_line.gantt_project {
    background-color: #1d4ed8;
    border-color: #1e40af;
    border-radius: 4px;
    height: 32px !important;
    line-height: 32px !important;
    font-weight: 700;
    font-size: 14px;
}

/* Modo Dark - Proyecto */
.dark .gantt_task_line.gantt_project {
    background-color: #3b82f6;
    border-color: #2563eb;
    color: #ffffff;
}

.gantt_task_line.weekend {
    background-color: rgba(37, 99, 235, 0.1);
    border-color: rgba(37, 99, 235, 0.2);
}

/* Escala y Grid */
.gantt_grid_scale,
.gantt_task_scale {
    background-color: #f8fafc;
    color: #1e293b;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
    height: 40px !important;
    line-height: 40px !important;
    font-size: 13px;
    vertical-align: middle;
}

/* Modo Dark - Escala y Grid */
.dark .gantt_grid_scale,
.dark .gantt_task_scale {
    background-color: #1e293b;
    color: #f8fafc;
    border-bottom: 1px solid #334155;
}

.gantt_grid_data .gantt_cell {
    border-right: 1px solid #e2e8f0;
    color: #1e293b;
    padding: 0 12px;
    font-size: 13px;
    font-weight: 500;
    height: 35px !important;
    line-height: 35px !important;
    vertical-align: middle;
}

/* Modo Dark - Celdas */
.dark .gantt_grid_data .gantt_cell {
    border-right: 1px solid #334155;
    color: #f8fafc;
    background-color: #0f172a;
}

.gantt_grid_data .gantt_cell.gantt_last_cell {
    border-right: none;
}

.gantt_row {
    border-bottom: 1px solid #e2e8f0;
    background-color: #ffffff;
}

.dark .gantt_row {
    border-bottom: 1px solid #334155;
    background-color: #1a1f2e;
}

.gantt_row.odd {
    background-color: #f8fafc;
}

.dark .gantt_row.odd {
    background-color: #1e293b;
}

.gantt_task_cell.weekend {
    background-color: #f3f4f6;
}

/* Modo Dark - Celdas de fin de semana */
.dark .gantt_task_cell.weekend {
    background-color: #1f2937;
}

.gantt_task_row.gantt_selected {
    background-color: rgba(37, 99, 235, 0.1);
}

/* Modo Dark - Fila seleccionada */
.dark .gantt_task_row.gantt_selected {
    background-color: rgba(59, 130, 246, 0.2);
}

.gantt_task_line.gantt_selected {
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}

/* Modo Dark - Tarea seleccionada */
.dark .gantt_task_line.gantt_selected {
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
}

/* Barra de progreso */
.gantt_task_progress {
    background-color: #1e40af;
    border-radius: 4px;
    height: 100%;
    opacity: 0.8;
}

/* Modo Dark - Barra de progreso */
.dark .gantt_task_progress {
    background-color: #1d4ed8;
    opacity: 0.9;
}

/* Botones de escala */
.scale-button {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s;
}

.scale-button.active {
    background-color: #2563eb;
    color: #ffffff;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}

.dark .scale-button.active {
    background-color: #3b82f6;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.scale-button:not(.active) {
    background-color: #f1f5f9;
    color: #334155;
}

.dark .scale-button:not(.active) {
    background-color: #1e293b;
    color: #e2e8f0;
}

.scale-button:hover:not(.active) {
    background-color: #e2e8f0;
}

.dark .scale-button:hover:not(.active) {
    background-color: #334155;
}

/* Estilos del Formulario */
.task-form {
    background-color: #ffffff;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

/* Modo Dark - Formulario */
.dark .task-form {
    background-color: #0f172a;
    border: 1px solid #334155;
}

.task-form h3 {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.dark .task-form h3 {
    color: #ffffff;
}

.task-form label {
    color: #334155;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

.dark .task-form label {
    color: #e2e8f0;
}

.task-form input,
.task-form select {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    width: 100%;
    font-size: 0.875rem;
    font-weight: 500;
}

.dark .task-form input,
.dark .task-form select {
    background-color: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}

.task-form input:focus,
.task-form select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    outline: none;
}

.dark .task-form input:focus,
.dark .task-form select:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.1);
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
    background-color: #3b82f6;
    color: #ffffff;
    border: none;
}

.task-form button.save:hover {
    opacity: 0.9;
}

.task-form button.cancel {
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid #d1d5db;
}

.task-form button.cancel:hover {
    background-color: #e2e8f0;
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

.dark .today-tasks h3 {
    color: #ffffff;
}

.task-card {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.2s;
}

.dark .task-card {
    background-color: #1e293b;
    border-color: #334155;
}

.task-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.dark .task-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
}

.task-card .task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.task-card .task-title {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.875rem;
}

.dark .task-card .task-title {
    color: #f8fafc;
}

.task-card .task-type {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    background-color: #dbeafe;
    color: #2563eb;
}

.dark .task-card .task-type {
    background-color: #1e40af;
    color: #bfdbfe;
}

.task-card .task-dates {
    font-size: 0.813rem;
    color: #475569;
    margin: 0.5rem 0;
    font-weight: 500;
}

.dark .task-card .task-dates {
    color: #cbd5e1;
}

.task-card .progress-bar {
    background-color: #e2e8f0;
    border-radius: 9999px;
    height: 0.5rem;
    overflow: hidden;
    margin-top: 0.75rem;
}

.dark .task-card .progress-bar {
    background-color: #334155;
}

.task-card .progress-value {
    background-color: #3b82f6;
    height: 100%;
    border-radius: 9999px;
    transition: width 0.3s ease;
}

.dark .task-card .progress-value {
    background-color: #60a5fa;
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
    background-color: #f1f5f9;
    color: #475569;
}

.button-group button.save {
    background-color: #3b82f6;
    color: #ffffff;
}

.button-group button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.button-group button:not(:disabled).cancel:hover {
    background-color: #e2e8f0;
}

.button-group button:not(:disabled).save:hover {
    background-color: #2563eb;
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

/* Sidebar Styles */
.sidebar {
    background-color: #ffffff;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.dark .sidebar {
    background-color: #0f172a;
    border: 1px solid #334155;
}

.scale-controls {
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    gap: 0.5rem;
}

.dark .scale-controls {
    border-bottom-color: #334155;
}

/* Headers y títulos */
.gantt_grid_head_cell {
    color: #1e293b;
    font-weight: 600;
    font-size: 13px;
    height: 40px !important;
    line-height: 40px !important;
    vertical-align: middle;
}

.dark .gantt_grid_head_cell {
    color: #f8fafc;
}

/* Texto de progreso */
.gantt_task_progress_text {
    height: 100%;
    line-height: 24px !important;
    color: #ffffff;
    font-weight: 600;
    font-size: 12px;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
}

.dark .gantt_task_progress_text {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

/* Títulos de secciones */
.section-title,
.task-management-title {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.dark .section-title,
.dark .task-management-title {
    color: #ffffff;
}

/* Texto descriptivo bajo el título */
.sidebar p,
.task-form p {
    color: #475569;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.dark .sidebar p,
.dark .task-form p {
    color: #e2e8f0;
}

/* Ajuste para el texto "Gestión de Tareas" */
.section-title,
.task-management-title {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.dark .section-title,
.dark .task-management-title {
    color: #ffffff;
}

/* Ajuste para el texto descriptivo */
.task-description {
    color: #475569;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.dark .task-description {
    color: #e2e8f0;
}

/* Ajustes adicionales para el modo oscuro */
.dark .gantt_grid {
    background-color: #0f172a;
}

.dark .gantt_task {
    background-color: #0f172a;
}

.dark .gantt_task_row {
    background-color: #0f172a;
}

.dark .gantt_task_row.odd {
    background-color: #1e293b;
}

/* Ajuste para el texto dentro de las tareas */
.gantt_task_content {
    height: 100%;
    line-height: 24px !important;
    padding: 0 8px;
    color: #ffffff;
}

/* Títulos principales del sidebar */
.dark h3,
.dark .task-form h3,
.dark .today-tasks h3,
.dark .section-title,
.dark .task-management-title {
    color: #ffffff !important;
}

/* Texto descriptivo */
.dark p,
.dark .task-description {
    color: #94a3b8;
}

/* Ajuste específico para el título de Gestión de Tareas */
.dark .text-gray-900 {
    color: #ffffff !important;
}
</style> 