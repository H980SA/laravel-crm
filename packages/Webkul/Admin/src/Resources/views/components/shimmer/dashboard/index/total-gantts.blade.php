<div class="grid gap-4 rounded-lg border border-gray-200 bg-white px-4 py-4 dark:border-gray-800 dark:bg-gray-900">
    <!-- Pie Chart Shimmer -->
    <div class="flex flex-col items-center justify-center space-y-4">
        <!-- Circle Shimmer -->
        <div class="shimmer h-40 w-40 rounded-full"></div>

        <!-- Legend Shimmer -->
        <div class="flex flex-col space-y-3">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3">
                    <!-- Color Indicator -->
                    <div class="shimmer h-4 w-4 rounded-full"></div>
                    <!-- Text Indicator -->
                    <div class="shimmer h-4 w-24 rounded-md"></div>
                </div>
            @endfor
        </div>
    </div>
</div>
