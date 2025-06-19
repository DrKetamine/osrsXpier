<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\PathController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\AuditLogController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Auth;

Route::aliasMiddleware('admin', AdminMiddleware::class);

Route::get('/', function () {
    return view('home');
});

// Main actions page
Route::match(['get', 'post'], '/actions', [ActionController::class, 'index'])->name('actions.index');

// custom show path
Route::get('/actions/path-{path}', [PathController::class, 'show'])->name('actions.path.show');

Route::resource('paths', PathController::class);

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show']);
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'show']);
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $userId = Auth::id();

        $paths = \App\Models\Path::withCount('steps')
            ->where('user_id', $userId)
            ->get();

        return view('auth.dashboard', compact('paths'));
    });

    // Save filter preset
    Route::post('/filters/save', [FilterController::class, 'store'])->name('filters.save');

    // Load saved filter and redirect to actions
    Route::get('/filters/load/{filter}', [FilterController::class, 'load'])->name('filters.load');
});

Route::post('/logout', LogoutController::class)->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/audit-logs/download', [AuditLogController::class, 'download'])->name('admin.audit-logs.download');
});