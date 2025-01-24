<?php

namespace Webkul\Admin\Http\Controllers;

class GanttController extends Controller
{
    /**
     * Display Gantt view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::gantt.index');
    }
} 