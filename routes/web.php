<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionController;

Route::get('/', function () {
    return view('home');
});

Route::get('/actions', [ActionController::class, 'index']);