<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\PathController;
use App\Services\ActionTableService;

Route::get('/', function () {
    return view('home');
});

// Main actions page (filterable table)
Route::match(['get', 'post'], '/actions', [ActionController::class, 'index'])->name('actions.index');

// View a specific path under /actions/path-{id}
Route::get('/actions/path-{path}', [PathController::class, 'show'])->name('actions.path.show');

// Resource routes for managing paths (except show, since we handle it separately)
Route::resource('paths', PathController::class)->except(['show']);