<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Home Dashboard
Route::get('/', function () {
    return view('home');
})->name('home');

// Task Management Interface
Route::get('/tasks', function () {
    return view('tasks.index');
})->name('tasks.interface');

// Legacy routes for backwards compatibility
Route::get('/tasks/index', function () {
    return redirect()->route('tasks.interface');
})->name('tasks.index');

Route::get('/tasks/demo', function () {
    return view('tasks.demo');
})->name('tasks.demo');
