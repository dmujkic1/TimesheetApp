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
    Route::post('add', [EmployeeController::class, 'create']); // Prikaz forme
    Route::post('store', [EmployeeController::class, 'store']); // Slanje forme
    Route::get('getAll', [EmployeeController::class, 'index']);
    Route::get('get/{employeeId}', [EmployeeController::class, 'show']);
    Route::put('modify/{employeeId}', [EmployeeController::class, 'update']);
    Route::delete('delete/{employeeId}', [EmployeeController::class, 'destroy']);
});