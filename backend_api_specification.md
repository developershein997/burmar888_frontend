# Guest Account Creation API Specification

## Endpoint: POST /api/guest-register

### Request Body
```json
{
  "name": "Guest1234", // Random guest name (auto-generated)
  "user_name": "ABC12", // 5-character unique username (auto-generated)
  "phone": null,
  "password": "gscplus", // Default password
  "password_confirmation": "gscplus", // Password confirmation
  "agent_id": null, // parent_id for guest accounts
  "status": 1,
  "is_changed_password": 1,
  "type": 40, // UserType::Player value
  "referral_code": null
}
```

### Response (Success - 200/201)
```json
{
  "status": "success",
  "message": "Guest account created successfully",
  "data": {
    "user": {
      "id": 123,
      "name": "Guest1234",
      "user_name": "ABC12",
      "phone": null,
      "type": 40,
      "status": 1,
      "is_changed_password": 1,
      "agent_id": null,
      "referral_code": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

### Response (Error - 422)
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "user_name": ["The user name has already been taken."]
  }
}
```

## Laravel Controller Implementation

```php
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
            'user_name' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|string|in:Player',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users',
            'profile' => 'nullable|string|max:2000',
            'agent_id' => 'nullable|exists:users,id',
            'payment_type_id' => 'nullable|integer',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'line_id' => 'nullable|string|max:255',
            'referral_code' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'site_link' => 'nullable|string|max:255',
            'shan_agent_name' => 'nullable|string|max:255',
            'shan_secret_key' => 'nullable|string|max:255',
            'shan_call_back_url' => 'nullable|string|max:255',
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
                'user_name' => $request->user_name,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'profile' => $request->profile,
                'max_score' => 0.00,
                'status' => 1,
                'is_changed_password' => 1, // Default password not changed
                'agent_id' => $request->agent_id,
                'payment_type_id' => $request->payment_type_id,
                'agent_logo' => 'default.png',
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'line_id' => $request->line_id,
                'commission' => 0.00,
                'referral_code' => $request->referral_code,
                'site_name' => $request->site_name,
                'site_link' => $request->site_link,
                'type' => $request->type,
                'shan_agent_code' => 'ABC123',
                'shan_agent_name' => $request->shan_agent_name,
                'shan_secret_key' => $request->shan_secret_key,
                'shan_call_back_url' => $request->shan_call_back_url,
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
```

## Routes Configuration

Add this route to your `routes/api.php`:

```php
Route::post('/guest-register', [App\Http\Controllers\Api\GuestRegisterController::class, 'register']);
```

## Key Features

1. **Auto-generated Username**: 5-character unique username using UserType::usernameLength(Player) = 5
2. **Default Password**: "billionbet" as specified
3. **User Type**: Set to "Player" (UserType::Player = 40)
4. **Default Values**: 
   - status = 1 (active)
   - is_changed_password = 1 (default password not changed)
   - max_score = 0.00
   - commission = 0.00
   - agent_logo = "default.png"
   - shan_agent_code = "ABC123"
5. **Token Authentication**: Uses Laravel Sanctum for API authentication
6. **Validation**: Ensures unique usernames and proper data validation 