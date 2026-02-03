<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MaintenanceRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Admin Registration (Separate/Hidden)
Route::get('/register-admin', [RegisterController::class, 'showAdminRegistrationForm'])->name('register.admin.form');
Route::post('/register-admin', [RegisterController::class, 'registerAdmin'])->name('register.admin');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Core Modules
    Route::get('rents/{rent}/agreement', [RentController::class, 'agreement'])->name('rents.agreement');
    Route::post('rents/{rent}/upload-agreement', [RentController::class, 'uploadAgreement'])->name('rents.upload-agreement');
    
    // User Status Toggle
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('units', UnitController::class);
    Route::resource('tenants', TenantController::class);
    Route::resource('rents', RentController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('maintenance', MaintenanceRequestController::class);
});
