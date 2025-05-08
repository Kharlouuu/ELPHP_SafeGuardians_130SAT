<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    //Register User
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['success' => 'User registered successfully']);
    }

    //Login User 
    public function login(Request $request)
    {
     $request->validate([
    'email' => 'required|email',
    'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user) {
        $passwordMatch = Hash::check($request->password, $user->password);
    
        // Logging for debug (will be saved in storage/logs/laravel.log)
        Log::info("Login attempt for {$request->email}");
        Log::info("Password match: " . ($passwordMatch ? 'yes' : 'no'));
    
        if ($passwordMatch) {
            return response()->json([
                'success' => 'Login successful',
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ]);
        }
    }
    
    return response()->json(['error' => 'Invalid credentials'], 401);
    }

    //Get all the users
    public function getAllUsers()
    {
        $users = DB::table('users')->select('id', 'username', 'email', 'created_at')->get();

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    //Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        if (!str_ends_with($request->email, '@gmail.com')) {
            return response()->json(['error' => 'Only Gmail addresses allowed']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not found']);
        }

        $code = rand(100000, 999999);
        $user->reset_code = $code;
        $user->save();

        try {
            Mail::send('emails.reset-code', ['code' => $code], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset Code');
            });
        } catch (\Exception $e) {
            // fallback to raw email if Blade fails
            Mail::raw("Your password reset code is: $code", function ($message) use ($user) {
                $message->to($user->email)->subject('Password Reset Code');
            });
        }

        return response()->json(['success' => 'Code sent']);
    }

    //Reset Password
    public function resetPassword(Request $request)
    {
    $request->validate([
        'email' => 'required|email',
        'reset_code' => 'required',
        'new_password' => 'required|min:6',
    ]);

    $user = User::where('email', $request->email)
                ->where('reset_code', $request->reset_code)
                ->first();

    if (!$user) {
        return response()->json(['error' => 'Invalid email or reset code'], 400);
    }

    $user->password = Hash::make($request->new_password);
    $user->reset_code = null; // clear the reset code once used
    $user->save();

    return response()->json(['success' => 'Password has been reset successfully']);
    }

    //Update Profile Activity
    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'new_username' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not found']);
        }

        $user->username = $request->new_username;
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => 'Profile updated successfully']);
    }
}