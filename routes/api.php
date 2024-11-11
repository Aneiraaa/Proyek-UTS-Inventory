<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;

// Route untuk login tanpa middleware
Route::post('/login', [AuthController::class, 'login']);
// Mengamankan semua route lainnya dengan middleware auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route CRUD dan laporan untuk Item
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    // Route untuk kategori
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Route untuk supplier
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

    // Route laporan tambahan
    Route::get('/stock-summary', [ItemController::class, 'stockSummary']);
    Route::get('/low-stock-items/{threshold}', [ItemController::class, 'lowStockItems']);
    Route::get('/items-by-category/{categoryId}', [ItemController::class, 'itemsByCategory']);
    Route::get('/category-summary', [ItemController::class, 'categorySummary']);
    Route::get('/supplier-summary', [ItemController::class, 'supplierSummary']);
    Route::get('/system-summary', [ItemController::class, 'systemSummary']);
});
