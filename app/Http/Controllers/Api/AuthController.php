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
        
        // جلب بيانات الموظف المرتبط بهذا المستخدم
        $employee = $user->employee; 

        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $employee ? $employee->full_name : $user->name,
                'email' => $user->email,
                'job_title' => $employee ? $employee->job_title : 'موظف',
                // توليد رابط الصورة الكامل هنا
                'profile_photo_url' => ($employee && $employee->profile_photo) 
                    ? asset('storage/' . $employee->profile_photo) 
                    : null,
                'vacation_balance' => $employee ? $employee->total_leave_balance : 0,
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