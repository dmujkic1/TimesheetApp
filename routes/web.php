<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ManagerTimesheetController;
use App\Http\Controllers\OOOController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TimeSheetController;
use App\Models\Manager;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::prefix('employees')->group(function () {
        Route::get('add', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('store', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('getAll', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('get/{employeeId}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('edit/{employeeId}', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('update/{employeeId}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('delete/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    Route::prefix('managers')->group(function () {
        Route::get('add', [ManagerController::class, 'create'])->name('managers.create');
        Route::get('getAll', [ManagerController::class, 'index'])->name('managers.index');
    });

    Route::prefix('projects')->group(function() {
        Route::get('add', [ProjectController::class, 'create'])->name('projects.create');
        Route::get('getAll', [ProjectController::class, 'index'])->name('projects.index');        
        Route::post('store', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('get/{projectId}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('edit/{projectId}', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('update/{projectId}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/delete/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::get('archive/{projectId}', [ProjectController::class, 'archive'])->name('projects.archive');
    });

    Route::prefix('teams')->group(function () {
        Route::get('assign', [TeamController::class, 'assignForm'])->name('teams.assign.form');
        Route::post('assign', [TeamController::class, 'assignEmployees'])->name('teams.assign');
    });

    Route::prefix('timesheets')->group(function () {
        Route::get('/', [TimesheetController::class, 'index'])->name('timesheets.index');
        Route::get('/entries', [TimesheetController::class, 'entries'])->name('timesheets.entries');
        Route::post('/store', [TimesheetController::class, 'store'])->name('timesheets.store');
        Route::get('/daily-summary', [TimesheetController::class, 'dailyWorkSummary'])->name('dailySummary');
        Route::delete('/delete/{timesheet}', [TimesheetController::class, 'destroy'])->name('timesheets.destroy');
        Route::put('/update/{timesheet}', [TimesheetController::class, 'update'])->name('timesheets.update');
        Route::post('/submit-month', [TimesheetController::class, 'submitMonth'])->name('timesheets.submitMonth');
    });

    Route::prefix('manager/timesheets')->name('manager.timesheets.')->group(function () {
        Route::get('/pending-approvals', [ManagerTimesheetController::class, 'index'])->name('pending');
        Route::patch('/approve/{timesheet}', [ManagerTimesheetController::class, 'approveTimesheetEntry'])->name('approveEntry');
        Route::patch('/reject/{timesheet}', [ManagerTimesheetController::class, 'rejectTimesheetEntry'])->name('rejectEntry');
    });

    Route::prefix('ooo')->group(function () {
        Route::post('/store', [OOOController::class, 'store'])->name('ooo.store');
    });

    Route::prefix('reporting')->group(function () {
        Route::get('/hours-per-user', [ReportController::class, 'hoursPerUser'])->name('reporting.hoursPerUser');
        Route::get('/hours-per-user/export', [ReportController::class, 'exportHoursPerUser'])->name('reporting.hoursPerUser.export');
    });

    //Fallback/Catchall Route
    Route::fallback(function () {
        return Inertia::render('web/screens/404error');
    });
});
