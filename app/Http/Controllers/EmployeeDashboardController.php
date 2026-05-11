<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Reward;
use App\Models\Penalty;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\TicketAllowance;
use App\Models\TicketTransaction;
use App\Models\User;


class EmployeeDashboardController extends Controller
{
   public function index()
{
    $user = auth()->user();

    // جلب الموظف مع العلاقات
    $employee = $user->employee ? $user->employee->load(['leaves', 'ticketTransactions']) : null;

    if (!$employee) {
        return redirect('/home')->with('error', 'الحساب غير مرتبط بموظف.');
    }

    // 1. حساب مجموع دقائق التأخير
    $totalLateMinutes = \App\Models\Attendance::where('employee_id', $employee->id)
        ->whereMonth('date', \Carbon\Carbon::now()->month)
        ->sum('late_minutes');

    $totalLateHours = round($totalLateMinutes / 60, 1);

    // 2. جلب المكافآت والعقوبات
    $rewards = \DB::table('rewards')->where('employee_id', $employee->id)->latest()->get();
    $penalties = \DB::table('penalties')->where('employee_id', $employee->id)->latest()->get();

    // 3. [تعديل هام] جلب رصيد التذاكر المتاح للشهر الحالي
    // هذا المتغير هو الذي سيظهر رقم 48 في الواجهة
    $currentTicketAllowance = TicketAllowance::where('employee_id', $employee->id)
        ->where('month', Carbon::now()->month)
        ->where('year', Carbon::now()->year)
        ->first();

    $ticketTransactions = $employee->ticketTransactions; 

    // 4. حساب رصيد الإجازات
    $usedLeaves = $employee->leaves
        ->where('status', 'approved')
        ->where('leave_type', 'annual')
        ->sum('days_count');

    $remainingBalance = $employee->total_leave_balance - $usedLeaves;

    // إرسال المتغيرات للـ View
    return view('admin.EmployeeDashboard.dashboard', compact(
        'employee',
        'totalLateHours',
        'rewards',
        'penalties',
        'remainingBalance',
        'ticketTransactions',
        'currentTicketAllowance' // أضفنا هذا المتغير ليمرر للواجهة
    ));
}
    public function updateProfile(Request $request)
    {
        $employee = auth()->user()->employee;

        // 1. تحديث الصورة الشخصية
        if ($request->hasFile('photo')) {

            // حذف الصورة القديمة باستخدام الحقل الصحيح profile_photo
            if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
                Storage::disk('public')->delete($employee->profile_photo);
            }

            // تخزين الصورة الجديدة في مجلد profile_photos
            $path = $request->file('photo')->store('profile_photos', 'public');

            // تحديث الحقل الصحيح في قاعدة البيانات
            $employee->update(['profile_photo' => $path]);
        }


        // 2. تحديث كلمة المرور إذا تم إدخالها
        if ($request->filled('password')) {
            // جلب المستخدم الحالي عن طريق ID الموثق
            \App\Models\User::where('id', auth()->id())->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return back()->with('success', 'تم تحديث ملفك الشخصي وصورتك بنجاح');
    }
public function getDashboardData()
{
    $user = auth()->user();
    $employee = $user->employee;

    if (!$employee) {
        return response()->json(['status' => 'error', 'message' => 'الموظف غير موجود'], 404);
    }
    // حساب مجموع دقائق التأخير للشهر الحالي
    $totalLateMinutes = \App\Models\Attendance::where('employee_id', $employee->id)
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->sum('late_minutes');

    $totalLateHours = round($totalLateMinutes / 60, 1);

    // تجهيز رابط الصورة الكامل
    // إذا كانت الصورة موجودة ننشئ الرابط، وإذا لم تكن نضع NULL
    $photoUrl = $employee->profile_photo 
                ? asset('storage/' . $employee->profile_photo) 
                : null;

    $allowance = TicketAllowance::where('employee_id', $employee->id)
        ->where('month', now()->month)
        ->where('year', now()->year)
        ->first();

    $rewards = Reward::where('employee_id', $employee->id)->latest()->get();
    $penalties = Penalty::where('employee_id', $employee->id)->latest()->get();
    $salaries = \App\Models\PayrollReport::where('employee_id', $employee->id)->latest()->get();

   return response()->json([
        'status' => 'success',
        'user_data' => [
            'name' => $employee->full_name,
            'job_title' => $employee->job_title,
            'profile_photo_url' => $photoUrl,
            // أضف هذا السطر إذا كنت قد جهزت حقل تغيير كلمة المرور
            // 'must_change_password' => $user->must_change_password, 
        ],
        'rewards' => $rewards,
        'penalties' => $penalties,
        'salaries' => $salaries,
        'stats' => [
            'total_leave_balance' => $employee->total_leave_balance ?? 0,
            'current_month_delay' => $totalLateHours, // استخدام المتغير المحسوب بدقة
            'tickets_count'       => $allowance ? $allowance->current_balance : 0, 
            'total_rewards_sum'   => $rewards->sum('amount'),
            'total_penalties_sum' => $penalties->sum('amount'),
        ]
    ]);
}

public function updateProfileApi(Request $request)
{
    $user = auth()->user();
    $employee = $user->employee;

    if (!$employee) {
        return response()->json(['status' => 'error', 'message' => 'الموظف غير موجود'], 404);
    }

    // 1. تحديث الصورة الشخصية
    if ($request->hasFile('photo')) {
        // حذف الصورة القديمة
        if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        // تخزين الجديدة
        $path = $request->file('photo')->store('profile_photos', 'public');
        $employee->update(['profile_photo' => $path]);
    }

    // 2. تحديث كلمة المرور
    if ($request->filled('password')) {
        $user->update([
            'password' => Hash::make($request->password)
        ]);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'تم تحديث البيانات بنجاح',
        'profile_photo_url' => asset('storage/' . $employee->profile_photo)
    ]);
}

}
