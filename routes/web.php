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

Route::group(['middleware' => ['web', 'user']], function () {
    Route::prefix(config('app.admin_path'))->group(function () {
        Route::get('/gantt', [GanttController::class, 'index'])->name('admin.gantt.index');
    });
});

Route::prefix('admin')->group(function () {
    Route::prefix('gantt')->group(function () {
        Route::get('/data', [Webkul\Admin\Http\Controllers\GanttController::class, 'getData']);
        Route::get('/licitaciones', [Webkul\Admin\Http\Controllers\GanttController::class, 'getLicitaciones']);
    });
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin'], function () {
    Route::get('contacts/persons/search', [
        \Webkul\Admin\Http\Controllers\Contact\PersonController::class,
        'search'
    ])->name('admin.contacts.persons.search');
});
