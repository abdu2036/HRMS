<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\TicketTransactionController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\AttendanceDeviceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// مسار تسجيل الدخول (عام)
Route::post('/login', [AuthController::class, 'login']);

// كل المسارات أدناه تحتاج لتسجيل دخول
Route::middleware('auth:sanctum')->group(function () {
    
    // بيانات المستخدم
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user-profile', function (Request $request) {
        return $request->user()->load('employee');
    });

    // --- نظام الحضور والانصراف ---
    Route::post('/attendance/checkin', [AttendanceController::class, 'store']);
    Route::post('/attendance/checkout', [AttendanceController::class, 'updateFromMobile']);
    Route::get('/attendance/status', [AttendanceController::class, 'getStatus']);

    // --- نظام الإجازات ---
    Route::post('/vacations/request', [LeaveController::class, 'store']);
    Route::get('/vacations/my-requests', [LeaveController::class, 'myRequests']);

    // --- نظام السلف ---
    Route::post('/loans/request', [LoanController::class, 'store']);
    Route::get('/loans/my-history', [LoanController::class, 'myLoans']);

    // --- نظام التذاكر (تم إدخاله هنا ليعمل auth()->user()) ---
    Route::prefix('tickets')->group(function () {
        Route::post('/spend', [TicketTransactionController::class, 'store']); 
        Route::get('/status', [TicketTransactionController::class, 'myStatus']); 

    });
    // --- نظام  والعقوبات ---
Route::get('/my-penalties', [PenaltyController::class, 'getMyPenaltiesApi']);
    // --- الداشبورد والمكافآت ---
    Route::get('/dashboard/stats', [EmployeeDashboardController::class, 'getStats']);
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'getDashboardData']);

});

Route::middleware('auth:sanctum')->get('/my-notifications', function (Request $request) {
    $notifications = $request->user()->notifications()->latest()->get()->map(function($n) {
        return [
            'id' => $n->id,
            'title' => $n->data['title'],
            'body' => $n->data['body'],
            'type' => $n->data['type'],
            'created_at' => $n->created_at->diffForHumans(),
            'is_read' => $n->read_at !== null
        ];
    });

    // هذا المسار هو ما يبحث عنه الجهاز لإرسال البصمات
Route::any('/iclock/cdata', [AttendanceDeviceController::class, 'receivePushData']);
    return response()->json(['status' => 'success', 'data' => $notifications]);
});