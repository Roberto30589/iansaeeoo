<?php


use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlantController;

Route::get('/', function () {
    /*
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
    */
    return redirect('/dashboard');
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

    //----Rutas de administración---//
    // Roles
    Route::get('/admin/roles',[AdminController::class, 'roleView'])->name('roles');
    Route::get('/admin/roles/table',[AdminController::class, 'roleTable'])->name('roles/table');
    Route::post('/admin/roles/create',[AdminController::class, 'createRole'])->name('roles/create');
    Route::put('/admin/roles/{id}/update',[AdminController::class, 'updateRole'])->name('roles/update');
    // Permisos
    Route::get('/admin/permissions',[AdminController::class, 'permissionView'])->name('permissions');
    Route::get('/admin/permissions/table',[AdminController::class, 'permissionTable'])->name('permissions/table');
    Route::post('/admin/permissions/create',[AdminController::class, 'createPermission'])->name('permissions/create');
    Route::put('/admin/permissions/{id}/update',[AdminController::class, 'updatePermission'])->name('permissions/update');

    //usuarios
    Route::get('/users',[UserController::class, 'index'])->name('users');
    Route::get('/users/add',[UserController::class, 'add'])->name('users/add');
    Route::get('/users/table',[UserController::class, 'table'])->name('users/table');
    Route::post('/users/create',[UserController::class, 'create'])->name('users/create');
    Route::get('/users/{id}',[UserController::class, 'select'])->name('users/select');
    Route::post('/users/{id}/update',[UserController::class, 'update'])->name('users/update');

    //plantas
    Route::get('/plants',[PlantController::class, 'index'])->name('plants');
    Route::get('/plants/table',[PlantController::class, 'table'])->name('plants/table');
    Route::get('/plants/add',[PlantController::class, 'add'])->name('plants/add');
    Route::post('/plants/create',[PlantController::class, 'create'])->name('plants/create');
    Route::get('/plants/{id}',[PlantController::class, 'select'])->name('plants/select');
    Route::post('/plants/{id}/update',[PlantController::class, 'update'])->name('plants/update');
    
});
