<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{   //ADD NEW EXPENSES
    public function addExpense(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $userId = $validated['user_id'];
        $name = $validated['name'];
        $amount = $validated['amount'];

        // Get current savings (example: from a separate table or user field)
        $user = DB::table('users')->where('id', $userId)->first();
        $savings = $user->total_savings ?? 0;

        if ($savings >= $amount) {
            // Deduct the expense from total savings
            DB::table('users')->where('id', $userId)->update([
                'total_savings' => $savings - $amount,
            ]);

            // Log expense (optional table)
            DB::table('expenses')->insert([
                'user_id' => $userId,
                'name' => $name,
                'amount' => $amount,
                'created_at' => now(),
                'type' => 'Expenses'
            ]);

            // Return success
            return response()->json([
                'success' => true,
                'message' => 'You have successfully added an expense for this month',
                'expense_name' => $name,
                'expense_amount' => $amount,
                'expense_type' => 'Expenses',
                'new_total_savings' => $savings - $amount,
            ]);
        } else {
            return response()->json([
                'error' => 'Low amount. Not enough savings.'
            ], 400);
        }
    }
    //Display all the expenses added by the user
    public function getExpenses(Request $request)
    {
    $userId = $request->query('user_id');

    if (!$userId || !is_numeric($userId)) {
    return response()->json([
        'success' => false,
        'error' => 'Invalid or missing user_id'
    ], 400);
}

    $expenses = DB::table('expenses')
    ->where('user_id', $userId)
    ->orderBy('created_at', 'desc')
    ->get();

    return response()->json([
    'success' => true,
    'expenses' => $expenses,
    ]);
}
    public function updateExpense(Request $request, $id)
    {
    $validated = $request->validate([
        'name' => 'required|string',
        'amount' => 'required|numeric|min:0.01',
    ]);

    $expense = DB::table('expenses')->where('id', $id)->first();

    if (!$expense) {
        return response()->json(['error' => 'Expense not found'], 404);
    }

    DB::table('expenses')->where('id', $id)->update([
        'name' => $validated['name'],
        'amount' => $validated['amount'],
        'updated_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Expense updated successfully',
        'updated_data' => $validated
    ]);
}

    public function deleteExpense($id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();

        if (!$expense) {
            return response()->json(['error' => 'Expense not found'], 404);
        }

        DB::table('expenses')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
        ]);
    }


}