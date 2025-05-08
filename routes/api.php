<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\SavingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route (optional)
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working']);
});

// Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/add-expense', [ExpenseController::class, 'addExpense']);   
    Route::post('/add-savings', [SavingsController::class, 'addSavings']);
    
});