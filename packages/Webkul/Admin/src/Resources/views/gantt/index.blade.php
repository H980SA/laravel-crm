<x-admin::layouts>
    <x-slot:title>
        {{ __('admin::app.layouts.gantt') }}
    </x-slot>

    <div class="content full-page">
        <div class="flex gap-[16px] justify-between items-center max-sm:flex-wrap">
            <div class="flex gap-[10px] items-center">
                <p class="text-[20px] text-gray-800 dark:text-white font-bold">
                    {{ __('admin::app.layouts.gantt') }}
                </p>
            </div>
        </div>

        <div class="flex flex-col justify-center items-center mt-[60px]">
            <img
                src="{{ asset('admin/build/assets/gantt-empty.svg') }}"
                class="w-[120px] h-[120px] dark:invert dark:mix-blend-exclusion"
            />

            <div class="flex flex-col items-center mt-[20px]">
                <p class="text-[16px] text-gray-400 font-semibold">
                    {{ __('admin::app.layouts.gantt') }}
                </p>
            </div>
        </div>
    </div>
</x-admin::layouts> 