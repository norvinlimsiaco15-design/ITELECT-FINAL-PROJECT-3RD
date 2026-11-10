<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InventoryReportController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StockTransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::patch('profile', [AuthController::class, 'updateProfile']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('categories', [CategoryController::class, 'index'])
        ->middleware('permission:categories.view|services.view,sanctum');
    Route::post('categories', [CategoryController::class, 'store'])->middleware('permission:categories.create,sanctum');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->middleware('permission:categories.edit,sanctum');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:categories.delete,sanctum');

    Route::get('materials', [MaterialController::class, 'index'])->middleware('permission:services.view,sanctum');
    Route::post('materials', [MaterialController::class, 'store'])->middleware('permission:services.create,sanctum');
    Route::get('materials/{material}', [MaterialController::class, 'show'])->middleware('permission:services.view,sanctum');
    Route::put('materials/{material}', [MaterialController::class, 'update'])->middleware('permission:services.edit,sanctum');
    Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->middleware('permission:services.delete,sanctum');

    Route::get('stock-transactions', [StockTransactionController::class, 'index'])->middleware('permission:transactions.view,sanctum');
    Route::post('stock-transactions', [StockTransactionController::class, 'store'])->middleware('permission:transactions.create,sanctum');

    Route::get('reports/valuation', [ReportController::class, 'valuation'])->middleware('permission:reports.view,sanctum');
    Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->middleware('permission:reports.view,sanctum');

    Route::get('inventory-reports', [InventoryReportController::class, 'index'])->middleware('permission:reports.view,sanctum');
    Route::post('inventory-reports', [InventoryReportController::class, 'store'])->middleware('permission:reports.view,sanctum');
    Route::get('inventory-reports/{inventoryReport}', [InventoryReportController::class, 'show'])->middleware('permission:reports.view,sanctum');
    Route::post('inventory-reports/{inventoryReport}/submit', [InventoryReportController::class, 'submit'])->middleware('permission:reports.view,sanctum');

    Route::middleware('role:admin,sanctum')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
        Route::put('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);
    });
});
