<?php

use App\Http\Controllers\Api\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiItemController;


use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\TransactionController;

Route::apiResource('/items', ApiItemController::class);
Route::apiResource('/analytics', AnalyticsController::class);
// Route::patch('/api/items/{id}', ApiItemController::class, 'update');
Route::post('/stock/in', [StockController::class, 'stockIn']);
Route::post('/stock/out', [StockController::class, 'stockOut']);
Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/transactions/{itemId}', [TransactionController::class, 'show']);


Route::get('/summary', [AnalyticsController::class, 'getSummaryData']);
Route::get('/total-inventory', [AnalyticsController::class, 'getTotalInventoryData']);
Route::get('/stock-in', [AnalyticsController::class, 'getStockInData']);
Route::get('/stock-out', [AnalyticsController::class, 'getStockOutData']);
