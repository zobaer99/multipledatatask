<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Specific task routes - must be defined BEFORE resource routes
Route::post('tasks/bulk', [TaskController::class, 'bulkStore'])->name('api.tasks.bulk');
Route::get('tasks/stats', [TaskController::class, 'statistics'])->name('api.tasks.stats');

// Standard resource routes for tasks
Route::apiResource('tasks', TaskController::class)->names([
    'index' => 'api.tasks.index',
    'store' => 'api.tasks.store',
    'show' => 'api.tasks.show',
    'update' => 'api.tasks.update',
    'destroy' => 'api.tasks.destroy'
]);
