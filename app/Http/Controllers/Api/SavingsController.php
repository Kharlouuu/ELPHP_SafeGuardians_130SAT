<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingsController extends Controller
{
    public function addSavings(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $userId = $validated['user_id'];
        $name = $validated['name'];
        $amount = $validated['amount'];

        // Retrieve current savings for the user
        $user = DB::table('users')->where('id', $userId)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $currentSavings = $user->total_savings ?? 0;
        $newTotal = $currentSavings + $amount;

        // Update user's total savings
        DB::table('users')->where('id', $userId)->update([
            'total_savings' => $newTotal,
        ]);

        // Insert new savings record
        DB::table('savings')->insert([
            'user_id' => $userId,
            'name' => $name,
            'amount' => $amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'You have successfully added savings for this month',
            'savings_name' => $name,
            'savings_amount' => $amount,
            'new_total_savings' => $newTotal,
        ]);
    }

    //Get Savings for all users
    public function getSavings(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
        ]);

        $savings = DB::table('savings')
            ->where('user_id', $validated['user_id'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'savings' => $savings,
        ]);
    }

    //Can update users
    public function updateSavings(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $savings = DB::table('savings')->where('id', $id)->first();
        if (!$savings) {
            return response()->json(['error' => 'Savings entry not found'], 404);
        }

        DB::table('savings')->where('id', $id)->update([
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Savings record updated successfully',
            'updated_data' => $validated
        ]);
    }

    // Users can delete recent added Savings
    public function deleteSavings($id)
    {
        $savings = DB::table('savings')->where('id', $id)->first();
        if (!$savings) {
            return response()->json(['error' => 'Savings entry not found'], 404);
        }

        DB::table('savings')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Savings record deleted successfully',
        ]);
    }

}
