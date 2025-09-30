<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CsvMapperController;
use App\Http\Controllers\CsvPreviewController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RuleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseUploadController;


Route::get('/dashboard', [DashboardController::class, 'index']);
// ---------------------------------- Expense Upload ----------------------------------

// Route for preivew and mapping

Route::post('/csv/preview',[CsvPreviewController::class, 'preview']); // save in the Temp Storage
Route::post('/csv/save-mapper', [CsvMapperController::class, 'store']); # save in the Database
Route::get('/csv/fetch-mapper', [CsvMapperController::class, 'fetch']); # fetch from the Database

Route::post('/expenses/upload', [ExpenseUploadController::class, 'upload'])->name('expenses.upload');
Route::post('/expenses/save', [ExpenseUploadController::class, 'save']);

// ---------------- ---------------- Summary ----------------------------------
Route::get('/expenses/summary-by-category', [ExpenseUploadController::class, 'summaryByCategory']);


// ---------------------------------- Rules  ----------------------------------
Route::get('/rules',[RuleController::class,'index']);
Route::post('/rules',[RuleController::class,'store']);
Route::delete('/rules/{id}',[RuleController::class,'destroy']);
Route::put('/rules/{id}',[RuleController::class,'update']);


// ----------------------------------- Categories ----------------------------------
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

/*Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});*/
