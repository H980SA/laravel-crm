<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web'])->group(function () {
    Route::prefix('admin/gantt')->group(function () {
        Route::post('/tasks', [\Webkul\Admin\Http\Controllers\GanttController::class, 'store']);
        Route::put('/tasks/{id}', [\Webkul\Admin\Http\Controllers\GanttController::class, 'update']);
        Route::get('/data', [\Webkul\Admin\Http\Controllers\GanttController::class, 'getData']);
        Route::get('/licitaciones', [\Webkul\Admin\Http\Controllers\GanttController::class, 'getLicitaciones']);
    });
});
