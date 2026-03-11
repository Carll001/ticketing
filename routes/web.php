<?php

use App\Http\Controllers\ManageDepartmentController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskPresetController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('department')->name('department.')->group(function() {
        Route::get('/', [ManageDepartmentController::class, 'index'])->name('index');
        Route::get('/create', [ManageDepartmentController::class, 'create'])->name('create');
        Route::post('/', [ManageDepartmentController::class, 'store'])->name('store');
        Route::get('/{department}', [ManageDepartmentController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [ManageDepartmentController::class, 'edit'])->name('edit');
        Route::patch('/{department}', [ManageDepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [ManageDepartmentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('user')->name('user.')->group(function() {
        Route::get('/', [ManageUserController::class, 'index'])->name('index');
        Route::get('/create', [ManageUserController::class, 'create'])->name('create');
        Route::post('/', [ManageUserController::class, 'store'])->name('store');
        Route::get('/{user}', [ManageUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [ManageUserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [ManageUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [ManageUserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('task')->name('task.')->group(function() {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::post('/{task}/steps/{taskStep}/claim', [TaskController::class, 'claimStep'])->name('step.claim');
        Route::post('/{task}/steps/{taskStep}/respond', [TaskController::class, 'respondStep'])->name('step.respond');
        Route::post('/{task}/steps/{taskStep}/comment', [TaskController::class, 'commentStep'])->name('step.comment');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::patch('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        
    });

    Route::prefix('task-preset')->name('taskPreset.')->group(function() {
        Route::get('/', [TaskPresetController::class, 'index'])->name('index');
        Route::get('/create', [TaskPresetController::class, 'create'])->name('create');
        Route::post('/', [TaskPresetController::class, 'store'])->name('store');
        Route::get('/{taskPreset}', [TaskPresetController::class, 'show'])->name('show');
        Route::get('/{taskPreset}/edit', [TaskPresetController::class, 'edit'])->name('edit');
        Route::patch('/{taskPreset}', [TaskPresetController::class, 'update'])->name('update');
        Route::delete('/{taskPreset}', [TaskPresetController::class, 'destroy'])->name('destroy');
        
    });

    Route::prefix('transaction')
        ->name('transaction.')
        ->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
        });
});

require __DIR__.'/settings.php';
