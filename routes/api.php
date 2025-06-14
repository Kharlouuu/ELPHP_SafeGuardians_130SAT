<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\SavingsController;
use App\Http\Controllers\Api\SavingsManagementController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ChallengeController;

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
    //Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);

    //Get All Users
    Route::get('/users/list', [AuthController::class, 'getAllUsers']);

    //ManageTransaction supports ADD and GET
    Route::post('/manage/add-savings', [SavingsManagementController::class, 'addMonthlySavings']); //ADD
    Route::get('/manage/get-savings', [SavingsManagementController::class, 'getTotalSavings']); //GET

    //Transactions Expense
    Route::post('/add-expense', [ExpenseController::class, 'addExpense']); //ADD
    Route::get('/expenses/list', [ExpenseController::class, 'getExpenses']); //GET
    Route::put('/expenses/{id}', [ExpenseController::class, 'updateExpense']); //UPDATE
    Route::delete('/expenses/{id}', [ExpenseController::class, 'deleteExpense']); //DELETE

    //Transaction Savings
    Route::post('/add-savings', [SavingsController::class, 'addSavings']); //ADD
    Route::get('/savings/list', [SavingsController::class, 'getSavings']); //GET
    Route::put('/savings/{id}', [SavingsController::class, 'updateSavings']); //Update
    Route::delete('/savings/{id}', [SavingsController::class, 'deleteSavings']); //Delete

    //View all Transaction Expenses and Savings
    Route::get('/transactions', [TransactionController::class, 'getUserTransactions']);

    //Challenge and Add Goal
    Route::post('/add-goal', [ChallengeController::class, 'addGoal']); //Add Goal
    Route::delete('/delete-goal/{id}', [ChallengeController::class, 'deleteGoal']); //Delete Goal
    Route::post('/save-today', [ChallengeController::class, 'saveToday']); //Add Savings For this day
    });
