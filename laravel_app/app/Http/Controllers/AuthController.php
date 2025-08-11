<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\PlatformUser;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:platform_users',
            'phone' => 'required|string|max:20|unique:platform_users',
            'password' => 'required|string|min:6',
        ]);

        $user = PlatformUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $loginField = filter_var($request->input('name_or_email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $request->validate([
            'name_or_email' => 'required',
            'password' => 'required'
        ]);

        if (!Auth::guard('web')->attempt([$loginField => $request->name_or_email, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function deleteUser(Request $request)
    {
        $user = $request->user(); // authenticated user

        $user->delete(); // Soft delete

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function getAllInfo(Request $request)
    {
        $user = $request->user();

        $customFields = \App\Models\CustomField::where('user_id', $user->id)->get();

        return response()->json([
            'user' => $user,
            'custom_fields' => $customFields
        ]);
    }


    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:platform_users',
            'phone' => 'required|string|max:20|unique:platform_users',
            'password' => 'required|string|min:6',
            'validation_password' => 'required|string', // ✅ new validation field
        ]);

        // ✅ Check if validation password matches
        if ($request->validation_password !== 'admin') {
            return response()->json([
                'message' => 'Request denied: unauthorized'
            ], 401);
        }

        $admin = PlatformUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $admin,
            'token' => $token
        ], 201);
    }


    public function getUserById($id)
    {
        $user = PlatformUser::withTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $user->toArray();

        // Add "status" field if deleted_at has a value
        if ($user->deleted_at) {
            $data['status'] = 'deleted';
            $data['deleted_at'] = Carbon::parse($user->deleted_at)->toDateTimeString();
        } else {
            $data['status'] = 'active';
        }

        return response()->json($data);
    }

    public function updateUser(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:platform_users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20|unique:platform_users,phone,' . $user->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }




}
