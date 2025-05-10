<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavedDay;
use App\Models\Goal;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    // Save today's amount with user_id
    public function saveToday(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $userId = $request->user_id;
        $today = Carbon::today()->format('Y-m-d');

        // Check if already saved for today
        $alreadySaved = SavedDay::where('user_id', $userId)
            ->where('date', $today)
            ->exists();

        if ($alreadySaved) {
            return response()->json(['message' => 'Already saved for today.'], 200);
        }

        // Save today's savings
        SavedDay::create([
            'user_id' => $userId,
            'date' => $today,
            'amount_saved' => $request->amount,
        ]);

        // Calculate monthly total
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $monthlyTotal = SavedDay::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Check for goal completion
        $goals = Goal::where('user_id', $userId)->get();
        $completedGoals = [];

        foreach ($goals as $goal) {
            if (!$goal->is_completed && $monthlyTotal >= $goal->goal_amount) {
                $goal->is_completed = true;
                $goal->save();
                $completedGoals[] = $goal->goal_name;
            }
        }

        return response()->json([
            'message' => 'Saved successfully.',
            'monthly_total' => round($monthlyTotal, 2),
            'completed_goals' => $completedGoals,
        ]);
    }

    // User will add Goal
    public function addGoal(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'goal_name' => 'required|string|max:255',
            'goal_amount' => 'required|numeric|min:1',
        ]);

        $goal = Goal::create([
            'user_id' => $request->user_id,
            'goal_name' => $request->goal_name,
            'goal_amount' => $request->goal_amount,
            'is_completed' => false,
        ]);

        return response()->json([
            'message' => 'Goal added successfully.',
            'goal' => $goal,
        ], 201);
    }

    // Delete goal by id and user_id
    public function deleteGoal(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $goal = Goal::where('user_id', $request->user_id)
            ->where('id', $id)
            ->first();

        if (!$goal) {
            return response()->json(['message' => 'Goal not found.'], 404);
        }

        $goal->delete();

        return response()->json(['message' => 'Goal deleted successfully.']);
    }
}