<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TimeTableController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SettingController;

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    // Admin Only - Core Structural Changes
    Route::middleware(['role:admin'])->group(function () {
        Route::post('classes/bulk-delete', [ClassController::class, 'bulkDelete'])->name('classes.bulk_delete');
        Route::resource('classes', ClassController::class);
        Route::post('sections/bulk-delete', [SectionController::class, 'bulkDelete'])->name('sections.bulk_delete');
        Route::resource('sections', SectionController::class);
        Route::resource('timetables', TimeTableController::class);
        Route::resource('devices', DeviceController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        // System Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Admin + Accountant + Operator - User Management
    Route::middleware(['role:admin|accountant|operator'])->group(function () {
        Route::get('users/import', [StudentImportController::class, 'show'])->name('users.import');
        Route::post('users/import', [StudentImportController::class, 'import'])->name('users.import.process');
        Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk_delete');
        Route::resource('users', UserController::class);
    });

    // Admin + Accountant - HR/Scheduler
    Route::middleware(['role:admin|accountant'])->group(function () {
        Route::resource('holidays', HolidayController::class);
        Route::resource('leaves', LeaveController::class);
    });

    // Admin + Teacher + Accountant - Reports
    Route::middleware(['role:admin|accountant|teacher'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/monthly/export', [ReportController::class, 'monthlyExport'])->name('reports.monthly.export');
        Route::get('reports/individual/{user}', [ReportController::class, 'individual'])->name('reports.individual');
        Route::get('reports/individual/{user}/export', [ReportController::class, 'individualExport'])->name('reports.individual.export');
    });

    // Admin + Operator + Teacher - Attendance Ops
    Route::middleware(['role:admin|operator|teacher'])->group(function () {
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
        Route::post('attendances/bulk-delete', [AttendanceController::class, 'bulkDelete'])->name('attendances.bulk_delete');
        Route::post('attendances/sync-trigger', [AttendanceController::class, 'triggerSync'])->name('attendances.sync_trigger');
    });
});
