<?php

namespace Webkul\Admin\View\Components\Gantt;

use Illuminate\View\Component;

class GanttComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public $tasks = [],
        public $licitaciones = [],
        public $todayTasks = []
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin::components.gantt.gantt-component');
    }
} 