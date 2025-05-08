<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Savings;
use App\Models\User;

class SavingsController extends Controller
{
    /**
     * Store a newly created saving.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name'    => 'required|string|max:255',
            'amount'  => 'required|numeric|min:0.01',
        ]);

        // Create the saving record
        $savings = Savings::create([
            'user_id' => $validated['user_id'],
            'name'    => $validated['name'],
            'amount'  => $validated['amount'],
        ]);

        return response()->json([
            'success' => 'You have successfully added savings for this month.',
            'savings' => $savings,
        ]);
    }

    /**
     * Get all savings for a user.
     */
    public function index($userId)
    {
        $savings = Savings::where('user_id', $userId)->get();
        return response()->json($savings);
    }
}