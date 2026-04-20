<?php

use App\Http\Controllers\Projects\StoreController as StoreProjectController;
use App\Http\Controllers\Tasks\DestroyController as DestroyTaskController;
use App\Http\Controllers\Tasks\IndexController as IndexTaskController;
use App\Http\Controllers\Tasks\ReorderController as ReorderTaskController;
use App\Http\Controllers\Tasks\StoreController as StoreTaskController;
use App\Http\Controllers\Tasks\UpdateController as UpdateTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexTaskController::class)->name('tasks.index');
Route::post('/tasks', StoreTaskController::class)->name('tasks.store');
Route::post('/tasks/reorder', ReorderTaskController::class)->name('tasks.reorder');
Route::put('/tasks/{task}', UpdateTaskController::class)->name('tasks.update');
Route::delete('/tasks/{task}', DestroyTaskController::class)->name('tasks.destroy');

Route::post('/projects', StoreProjectController::class)->name('projects.store');
