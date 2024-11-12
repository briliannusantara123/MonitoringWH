<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/monitoring', [MonitoringController::class, 'index']);
    Route::get('/users', [AuthController::class, 'users'])->name('users.index');
    Route::post('/createuser', [AuthController::class, 'create_user']);
    Route::put('/users/{id}', [AuthController::class, 'update_users'])->name('users.update');
    Route::delete('/users/{id}', [AuthController::class, 'hapus_users'])->name('users.hapus_users');

});



