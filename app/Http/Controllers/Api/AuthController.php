<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

// Add HasApiTokens trait to User model


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. التأكد من وصول البيانات
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. محاولة تسجيل الدخول
        if (Auth::attempt($credentials)) {
         /** @var \App\Models\User $user **/
$user = Auth::user();
$token = $user->createToken('main')->plainTextToken;

           return response()->json([
    'user' => [
        'name' => $user->name,
        'email' => $user->email,
        'vacation_balance' => $user->vacation_balance, // رصيد الإجازات
        'tickets_balance' => $user->tickets_balance,   // رصيد التذاكر
    ],
    'token' => $token,
    'message' => 'Success'
], 200);
        }

        // 3. في حال فشل الدخول
        return response()->json([
            'message' => 'البيانات المدخلة غير صحيحة'
        ], 401);
    }
    
}