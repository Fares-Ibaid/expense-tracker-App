<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RuleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseUploadController;


Route::get('/dashboard', [DashboardController::class, 'index']);
// ---------------------------------- Expense Upload ----------------------------------
Route::post('/expenses/upload', [ExpenseUploadController::class, 'upload'])->name('expenses.upload');
Route::post('/expenses/save', [ExpenseUploadController::class, 'save']);

// ---------------- ---------------- Summary ----------------------------------
Route::get('/expenses/summary-by-category', [ExpenseUploadController::class, 'summaryByCategory']);


// ---------------------------------- Rules  ----------------------------------
Route::get('/rules',[RuleController::class,'index']);
Route::post('/rules',[RuleController::class,'store']);


/*Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});*/
