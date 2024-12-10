<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutletsController;
use App\Http\Controllers\CandlestickController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\TembakDataController;
use App\Http\Controllers\LogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.sendLink');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.resetForm');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::middleware(['auth'])->group(function(){
	Route::get('/dashboard', [DashboardController::class, 'index']);
	Route::post('/searchdashboard', [DashboardController::class, 'index']); 
	Route::get('/outlets', [OutletsController::class, 'index']);
	Route::get('/outlet/view/{id}/{start}/{end}', [OutletsController::class, 'view'])->name('outletsview');
	Route::post('/outlet/search', [OutletsController::class, 'search'])->name('outletssearch');
	Route::get('/users', [UsersController::class, 'users'])->name('users.index');
	Route::get('/adduser', [UsersController::class, 'adduser']);
	Route::post('/createuser', [UsersController::class, 'create_user']);
	Route::get('/edituser/{id}', [UsersController::class, 'edit'])->name('edituser');
	Route::put('/users/{id}', [UsersController::class, 'update_users'])->name('users.update');
	Route::delete('/users/{id}', [UsersController::class, 'hapus_users'])->name('users.hapus_users');
	Route::get('/settings', [SettingsController::class, 'index']);
	Route::post('/changePassword', [SettingsController::class, 'changePassword']);
	Route::post('/changeEmail', [SettingsController::class, 'changeEmail']);
	Route::get('/cekserver', [ServerController::class, 'index']);
	Route::get('/get-outlet-data', [DashboardController::class, 'getOutletData'])->name('getOutletData');
	Route::get('/log', [LogController::class, 'index']);
	Route::post('/searchlog', [LogController::class, 'index']);
	Route::post('/updateserver', [LogController::class, 'updateserver']);
	Route::post('/updateserverD', [LogController::class, 'updateserverD']);
});

Route::prefix('tembak-data')->group(function () {
    Route::get('/customer/{id}', [TembakDataController::class, 'customer'])->name('tembak.customer');
    Route::get('/transactions/{id}', [TembakDataController::class, 'transactions'])->name('tembak.transactions');
    Route::get('/details/{id}', [TembakDataController::class, 'details'])->name('tembak.details');
});

Route::get('/closetab', [TembakDataController::class, 'closetab']);
Route::get('/cekserverauto', [ServerController::class, 'auto']);
// Route::middleware(['auth', 'role:admin,staff'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index']);
//     Route::post('/searchdashboard', [DashboardController::class, 'index']);
//     Route::get('/outlets', [OutletsController::class, 'index']);
//     Route::get('/users', [AuthController::class, 'users'])->name('users.index');
//     Route::post('/createuser', [AuthController::class, 'create_user']);
//     Route::put('/users/{id}', [AuthController::class, 'update_users'])->name('users.update');
//     Route::delete('/users/{id}', [AuthController::class, 'hapus_users'])->name('users.hapus_users');

// });



