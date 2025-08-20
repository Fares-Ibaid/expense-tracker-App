<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseUploadController;


Route::get('/dashboard', [DashboardController::class, 'index']);
// ---------------------------------- Expense Upload ----------------------------------
Route::post('/expenses/upload', [ExpenseUploadController::class, 'upload'])->name('expenses.upload');
Route::post('/expenses/save', [ExpenseUploadController::class, 'save']);
