<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingsManagementController extends Controller
{
   public function getTotalSavings(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer',
    ]);

    $userId = $request->query('user_id');

    $user = DB::table('users')
    ->where('id', $userId)
    ->select('username', 'email', 'total_savings')
    ->first();

    if (!$user) {
    return response()->json([
        'success' => false,
        'message' => 'User not found'
    ], 404);
}

    return response()->json([
    'success' => true,
    'total_savings' => $user->total_savings,
    'username' => $user->username,
    'email' => $user->email,
    ]);
}

    public function addMonthlySavings(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $user = DB::table('users')->where('id', $validated['user_id'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found',
            ], 404);
        }

        $currentTotal = $user->total_savings ?? 0;
        $newTotal = $currentTotal + $validated['amount'];

        DB::table('users')->where('id', $validated['user_id'])->update([
            'total_savings' => $newTotal,
        ]);

        DB::table('savings_log')->insert([
            'user_id' => $validated['user_id'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? 'Savings top-up',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Savings added and logged successfully',
            'added_amount' => $validated['amount'],
            'new_total_savings' => $newTotal,
        ]);
    }
}