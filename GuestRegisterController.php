<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GuestRegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'agent_id' => 'nullable|exists:users,id',
            'status' => 'required|integer|in:1',
            'is_changed_password' => 'required|integer|in:1',
            'type' => 'required|integer|in:40',
            'referral_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the guest user
            $user = User::create([
                'name' => $request->name,
                'user_name' => $request->user_name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'agent_id' => $request->agent_id,
                'status' => $request->status,
                'is_changed_password' => $request->is_changed_password,
                'type' => $request->type,
                'referral_code' => $request->referral_code,
            ]);

            // Generate token
            $token = $user->createToken('guest-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Guest account created successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create guest account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 