<div class="flex items-center justify-between bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm">
    <div class="flex flex-col gap-2">
        <div class="flex cursor-pointer items-center">
            <!-- Breadcrumbs -->
            <div class="flex justify-start max-lg:hidden">
                <div class="flex items-center gap-x-3.5">
                    <nav aria-label="">
                        <ol class="flex flex-wrap">
                            <li class="flex items-center gap-x-1 text-sm font-normal text-brandColor dark:text-brandColor">
                                <a href="{{ route('admin.dashboard.index') }}">
                                    Dashboard
                                </a>
                                <span class="after:content-['/'] ltr:mr-1 rtl:ml-1"></span>
                            </li>

                            <li class="flex items-center gap-x-1 text-base text-gray-600 after:content-['/'] last:cursor-default after:last:hidden dark:text-gray-300" aria-current="page">
                                @lang('admin::app.gantt.index.title')
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="text-xl font-bold dark:text-white">
            @lang('admin::app.gantt.index.title')
        </div>
    </div>
</div>