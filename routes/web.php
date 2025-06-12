<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/ping-db', function () {
    try {
        \DB::connection()->getPdo();
        return '✅ DB OK: ' . \DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return '❌ DB ERROR: ' . $e->getMessage();
    }
});

Route::get('/run-migrate', function () {
    \Artisan::call('migrate', ['--force' => true]);
    return 'Migración completada';
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
