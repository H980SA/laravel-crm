<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\GanttController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web', 'admin', 'admin.auth']], function () {
    Route::prefix(config('app.admin_path'))->group(function () {
        Route::get('/gantt', [GanttController::class, 'index'])->name('admin.gantt.index');
    });
});
