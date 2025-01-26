{!! view_render_event('admin.leads.index.table.before') !!}
@php
    $stageValue =$initial_filters['initial_filters']['columns'][0]['value'] ?? null;
    if($stageValue){
        $stageValue = (string) $stageValue;
    }

@endphp

<x-admin::datagrid 
    :src="route('admin.leads.index')" 
    initial-filters="{{$stageValue}}"
>
    <!-- DataGrid Shimmer -->
    <x-admin::shimmer.datagrid />
    <x-slot:toolbar-right-after>
        @include('admin::leads.index.view-switcher')
    </x-slot>
</x-admin::datagrid>

{!! view_render_event('admin.leads.index.table.after') !!}