<?php

use App\Http\Controllers\EmployeeController;
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

Route::prefix('employees')->group(function () {
    Route::get('add', [EmployeeController::class, 'create'])->name('employees.create'); // Prikaz forme
    Route::post('store', [EmployeeController::class, 'store'])->name('employees.store'); // Slanje forme
    Route::get('getAll', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('get/{employeeId}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('edit/{employeeId}', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('update/{employeeId}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('delete/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});