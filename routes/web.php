<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\PermissionController;

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
Route::post('logout', [HomeController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('profile', [UserController::class, 'showProfile']);
    Route::post('profile', [UserController::class, 'updateProfile']);
    Route::get('change-password', [UserController::class, 'showChangePassword'])->name('password.change');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('password.update');
});

Route::prefix('user')->middleware('auth')->group(function () {
    
    /* user routes */
    Route::get('add-user', [UserController::class, 'showAddUser']);
    Route::post('add-user', [UserController::class, 'add_user']);
    Route::post('ajax_user_check_record', [UserController::class, 'ajax_user_check_record']);
    Route::post('get_role_reporting', [UserController::class, 'get_role_reporting'])->name('get_role_reporting');
    Route::post('get_department_record', [UserController::class, 'get_department_record'])->name('get_department_record');
    Route::post('get_designation_record', [UserController::class, 'get_designation_record'])->name('get_designation_record');

    Route::get('user', [UserController::class, 'user'])->name('user');
    Route::post('user_list', [UserController::class, 'user_list']);

    Route::get('edit-user/{id}', [UserController::class, 'showEditUser']);
    Route::post('update_user', [UserController::class, 'update_user']);
    Route::post('user_delete', [UserController::class, 'user_delete']);
    Route::get('user-details/{id}', [UserController::class, 'showUserDetails'])->name('user-details');
    Route::post('user_tab_detail', [UserController::class, 'user_tab_detail'])->name('user_tab_detail');
    Route::get('user_document_list_tab', [UserController::class, 'user_document_list_tab']);
    Route::post('add_user_document', [UserController::class, 'add_user_document']);
    Route::post('user_document_edit', [UserController::class, 'user_document_edit']);
    Route::get('access_rights_user_document_list_tab', [UserController::class, 'access_rights_user_document_list_tab']);
    Route::post('user_document_access_save', [UserController::class, 'user_document_access_save']);

    Route::get('user_other_document_list_tab', [UserController::class, 'user_other_document_list_tab']);
    Route::post('vessel_check_in_out_list_tab', [UserController::class, 'vessel_check_in_out_list_tab'])->name('vessel_check_in_out_list_tab');

    Route::post('vessel_apprisal_list_tab', [UserController::class, 'vessel_apprisal_list_tab'])->name('vessel_apprisal_list_tab');
    Route::post('vessel_apprisal_list_edit', [UserController::class, 'vessel_apprisal_list_edit'])->name('vessel_apprisal_list_edit');
    Route::post('add_update_vessel_apprisal', [UserController::class, 'add_update_vessel_apprisal'])->name('add_update_vessel_apprisal');

    /* user menu permission routes */
    Route::get('menu-user-permission/{id}', [PermissionController::class, 'menu_user_permission'])->name('menu_user_permission');
    Route::post('menu-user-permission-store', [PermissionController::class, 'menu_user_permission_store'])->name('menu_user_permission_store');
});
