<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function getUserTransactions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $userId = $request->query('user_id');

        // Fetch expenses
        $expenses = DB::table('expenses')
            ->where('user_id', $userId)
            ->select('id', 'name', 'amount', 'type', 'created_at')
            ->get();

        // Fetch savings
        $savings = DB::table('savings')
            ->where('user_id', $userId)
            ->select('id', 'name', 'amount', DB::raw("'Savings' as type"), 'created_at')
            ->get();

        // Merge both collections and sort by date descending
        $transactions = $expenses->merge($savings)->sortByDesc('created_at')->values();

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }
}