<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseUploadController;
use Illuminate\Support\Facades\Route;


// ---------------------------------- Dashboard ----------------------------------
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('dashboard', [DashboardController::class, 'index']);
// ---------------------------------- Expenses ----------------------------------
Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
// ---------------------------------- Expense Upload ----------------------------------
Route::post('/expenses/upload', [ExpenseUploadController::class, 'upload'])->name('expenses.upload');
Route::post('/expenses/save', [ExpenseUploadController::class, 'save']);


