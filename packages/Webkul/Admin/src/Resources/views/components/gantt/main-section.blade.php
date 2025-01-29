<div class="flex-1">
    <div class="flex flex-col gap-4">
        <!-- Header Section -->
        @include('admin::components.gantt.header')

        <!-- Gantt Component -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm">
            <!-- Scale Controls -->
            @include('admin::components.gantt.scale-controls')
            
            <!-- Gantt Container -->
            <div class="relative">
                <div id="gantt_here" style="width:100%; height:600px; overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>