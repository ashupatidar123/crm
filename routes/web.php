<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
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

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
//Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('logout', [HomeController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset']);


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/userList', [UserController::class, 'userList']);
    Route::get('user_export', function () {
        return Excel::download(new UsersExport, 'users.xlsx');
    });

    Route::get('/profile', [UserController::class, 'showProfile']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::get('/change-password', [UserController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('password.update');


    Route::get('/advanced-form', [UserController::class, 'advanced_form']);
    Route::get('/user-tables', [UserController::class, 'user_tables']);
});
