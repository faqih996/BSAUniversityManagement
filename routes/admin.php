<?php

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\FacultyController;
use App\Models\Faculty;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth', 'role:Admin')->group(function () {
    Route::get('dashboard', DashboardAdminController::class)->name('admin.dashboard');

    Route::controller(FacultyController::class)->group(function () {
        Route::get('faculties', 'index')->name('admin.faculties.index');
        Route::get('faculties/create', 'create')->name('admin.faculties.create');
        Route::post('faculties/create', 'store')->name('admin.faculties.store');
        Route::get('faculties/edit/{faculty:slug}', 'edit')->name('admin.faculties.edit');
        Route::put('faculties/edit/{faculty:slug}', 'update')->name('admin.faculties.update');
        Route::delete('faculties/destroy/{faculty:slug}', 'destroy')->name('admin.faculties.destroy');
    });

    Route::controller(DepartmentController::class)->group(function () {
        Route::get('departments', 'index')->name('admin.departments.index');
        Route::get('departments/create', 'create')->name('admin.departments.create');
        Route::post('departments/create', 'store')->name('admin.departments.store');
        Route::get('departments/edit/{department:slug}', 'edit')->name('admin.departments.edit');
        Route::put('departments/edit/{department:slug}', 'update')->name('admin.departments.update');
        Route::delete('departments/destroy/{department:slug}', 'destroy')->name('admin.departments.destroy');
    });
});
