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
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin Only - Core Structural Changes
    Route::middleware(['role:admin'])->group(function () {
        Route::post('classes/bulk-delete', [ClassController::class, 'bulkDelete'])->name('classes.bulk_delete');
        Route::resource('classes', ClassController::class)->except(['show']);
        Route::post('sections/bulk-delete', [SectionController::class, 'bulkDelete'])->name('sections.bulk_delete');
        Route::resource('sections', SectionController::class)->except(['show']);
        Route::resource('timetables', TimeTableController::class)->except(['show']);
        Route::resource('devices', DeviceController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', PermissionController::class)->except(['show']);

        // System Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Admin + Accountant + Operator - User Management
    Route::middleware(['role:admin|accountant|operator'])->group(function () {
        Route::get('students/import', [StudentImportController::class, 'show'])->name('students.import');
        Route::get('students/import/demo', [StudentImportController::class, 'downloadDemo'])->name('students.import.demo');
        Route::post('students/import', [StudentImportController::class, 'import'])->name('students.import.process');
        Route::get('users/import', [StudentImportController::class, 'show'])->name('users.import');
        Route::get('users/import/demo', [StudentImportController::class, 'downloadDemo'])->name('users.import.demo');
        Route::post('users/import', [StudentImportController::class, 'import'])->name('users.import.process');
        Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk_delete');
        Route::resource('students', StudentController::class);
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Admin + Accountant - HR/Scheduler
    Route::middleware(['role:admin|accountant'])->group(function () {
        Route::resource('holidays', HolidayController::class)->except(['show']);
        Route::resource('leaves', LeaveController::class)->except(['show']);
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

    // Admin + Operator + Teacher - Attendance viewing
    Route::middleware(['role:admin|operator|teacher'])->group(function () {
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    });

    // Admin + Operator - Attendance operations
    Route::middleware(['role:admin|operator'])->group(function () {
        Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
        Route::post('attendances/bulk-delete', [AttendanceController::class, 'bulkDelete'])->name('attendances.bulk_delete');
        Route::post('attendances/sync-trigger', [AttendanceController::class, 'triggerSync'])->name('attendances.sync_trigger');
    });
});
