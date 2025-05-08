<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
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
}