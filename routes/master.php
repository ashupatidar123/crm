<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentDesignationController;

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

/* master url routes */
Route::any('get_ajax_country', [RegionController::class,'get_ajax_country']);
Route::any('get_ajax_state', [RegionController::class,'get_ajax_state']);
Route::any('get_ajax_city', [RegionController::class,'get_ajax_city']);

Route::prefix('master')->middleware('auth')->group(function () {
    /* common routes */
    Route::post('ajax_active_inactive', [CommonController::class, 'ajax_active_inactive']);
    Route::post('ajax_delete', [CommonController::class, 'ajax_delete']);
    Route::post('ajax_view', [CommonController::class, 'ajax_view'])->name('ajax_view');

    /* user routes */
    Route::get('add-user', [UserController::class, 'showAddUser']);
    Route::post('add-user', [UserController::class, 'add_user']);
    Route::post('ajax_user_check_record', [UserController::class, 'ajax_user_check_record']);
    Route::post('get_role_reporting', [UserController::class, 'get_role_reporting'])->name('get_role_reporting');
    Route::post('get_department_record', [UserController::class, 'get_department_record'])->name('get_department_record');
    Route::post('get_designation_record', [UserController::class, 'get_designation_record'])->name('get_designation_record');

    Route::get('user', [UserController::class, 'user']);
    Route::get('user_list', [UserController::class, 'user_list']);

    Route::get('edit-user/{id}', [UserController::class, 'showEditUser']);
    Route::post('update_user', [UserController::class, 'update_user']);

    /* roles routes */
    Route::resource('role', RoleController::class);
    Route::get('role_list', [RoleController::class, 'role_list'])->name('role.list');
    Route::post('role_edit', [RoleController::class, 'role_edit'])->name('role.edit');

    /* department routes */
    Route::resource('department', DepartmentController::class);
    Route::get('department_list', [DepartmentController::class, 'department_list'])->name('department.list');
    Route::post('department_edit', [DepartmentController::class, 'department_edit'])->name('department.edit');

    /* department routes */
    Route::resource('designation', DepartmentDesignationController::class);
    Route::get('designation_list', [DepartmentDesignationController::class, 'designation_list'])->name('designation.list');
    Route::post('designation_edit', [DepartmentDesignationController::class, 'designation_edit'])->name('designation.edit');

    /* region routes */
    Route::get('region/country', [RegionController::class,'country']);
    Route::get('region/country_list', [RegionController::class,'country_list']);
    Route::post('region/country_delete', [RegionController::class,'country_delete']);
    Route::post('region/country_update', [RegionController::class,'country_update']);

    Route::get('region/state', [RegionController::class,'state']);
    Route::get('region/state_list', [RegionController::class,'state_list']);
    Route::post('region/state_delete', [RegionController::class,'state_delete']);
    Route::post('region/state_update', [RegionController::class,'state_update']);

    Route::get('region/city', [RegionController::class,'city']);
    Route::get('region/city_list', [RegionController::class,'city_list']);
    Route::post('region/city_delete', [RegionController::class,'city_delete']);
    Route::post('region/city_update', [RegionController::class,'city_update']);

    Route::post('region/region_active_inactive', [RegionController::class,'region_active_inactive']);


    Route::post('user_delete', [UserController::class, 'user_delete']);
});
