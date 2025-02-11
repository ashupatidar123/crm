<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentDesignationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\VesselController;
use App\Http\Controllers\VesselCategoryController;
use App\Http\Controllers\UserDocumentAccess;
use App\Http\Controllers\VesselCheckInOutController;
use App\Http\Controllers\MenuController;

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
Route::post('dropzone_file_upload', [CommonController::class, 'dropzone_file_upload'])->name('dropzone_file_upload');

Route::prefix('master')->middleware('auth')->group(function () {
    /* common routes */
    Route::post('ajax_active_inactive', [CommonController::class, 'ajax_active_inactive']);
    Route::post('ajax_delete', [CommonController::class, 'ajax_delete']);
    Route::post('ajax_view', [CommonController::class, 'ajax_view'])->name('ajax_view');

    /* document routes */
    Route::resource('document', DocumentController::class);
    Route::get('document_list', [DocumentController::class, 'document_list'])->name('document.list');
    Route::post('document_edit', [DocumentController::class, 'document_edit'])->name('document.edit');
    Route::post('get_parent_document', [DocumentController::class, 'get_parent_document'])->name('get_parent_document');

    /* roles routes */
    Route::resource('role', RoleController::class);
    Route::get('role_list', [RoleController::class, 'role_list'])->name('role.list');
    Route::post('role_edit', [RoleController::class, 'role_edit'])->name('role.edit');

    /* department routes */
    Route::resource('department', DepartmentController::class);
    Route::get('department_list', [DepartmentController::class, 'department_list'])->name('department.list');
    Route::post('department_edit', [DepartmentController::class, 'department_edit'])->name('department.edit');

    /* designation routes */
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

    /* menu routes */
    Route::resource('menu', MenuController::class);
    Route::post('menu_list', [MenuController::class, 'menu_list'])->name('menu_list');
    Route::post('menu_list_edit', [MenuController::class, 'menu_list_edit'])->name('menu_list_edit');
    Route::post('get_parent_menu', [MenuController::class, 'get_parent_menu'])->name('get_parent_menu');
});

Route::prefix('vessel')->middleware('auth')->group(function () {
    /* vessels routes */
    Route::resource('vessel', VesselController::class);
    Route::get('vessel_list', [VesselController::class, 'vessel_list'])->name('vessel.list');
    Route::get('vessel-details/{id}', [VesselController::class, 'showVesselDetails'])->name('vessel-details');
    Route::post('vessel_edit', [VesselController::class, 'vessel_edit']);
    Route::post('vessel_file_upload', [VesselController::class, 'vessel_file_upload']);
    Route::post('get_all_vessel_category', [VesselController::class, 'get_all_vessel_category'])->name('get_all_vessel_category');

    Route::post('vessel_tab_detail', [VesselController::class, 'vessel_tab_detail']);
    Route::get('vessel_document_list_tab', [VesselController::class, 'vessel_document_list_tab']);
    Route::post('add_vessel_document', [VesselController::class, 'add_vessel_document']);
    Route::post('vessel_document_edit', [VesselController::class, 'vessel_document_edit']);

    /* vessel category routes */
    Route::resource('vessel-category', VesselCategoryController::class);
    Route::get('vessel_category_list', [VesselCategoryController::class, 'vessel_category_list'])->name('vessel_category_list');
    Route::post('vessel_category_edit', [VesselCategoryController::class, 'vessel_category_edit'])->name('vessel_category_edit');
    Route::post('get_parent_vessel_category', [VesselCategoryController::class, 'get_parent_vessel_category'])->name('get_parent_vessel_category');

    /* vessel check in out routes */
    Route::resource('check-in-out', VesselCheckInOutController::class);
    Route::post('check_in_out_list', [VesselCheckInOutController::class, 'check_in_out_list'])->name('check_in_out_list');
    Route::post('check_in_out_edit', [VesselCheckInOutController::class, 'check_in_out_edit'])->name('check_in_out_edit');
    Route::post('check_out', [VesselCheckInOutController::class, 'check_out'])->name('check_out');
});
