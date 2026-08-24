<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Pendataan Alumni MNI IPB
|--------------------------------------------------------------------------
*/

// Guest Routes (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes (Mahasiswa & Admin)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [AlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/{id}', [AlumniController::class, 'show'])->name('alumni.show');

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/alumni/export/csv', [AlumniController::class, 'exportCsv'])->name('alumni.export');
        Route::post('/alumni/import/csv', [AlumniController::class, 'importCsv'])->name('alumni.import');
        Route::delete('/alumni/{id}', [AlumniController::class, 'destroy'])->name('alumni.destroy');
    });
});
