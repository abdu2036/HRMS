<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\RewardReceived;
use App\Notifications\HRSystemNotification; // استدعاء الإشعار الموحد
use Illuminate\Support\Facades\Auth;
class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::with('employee')->latest()->get();
        return view('admin.rewards.index', compact('rewards'));
    }
  public function store(Request $request)
{
    // 1. التحقق من البيانات القادمة من الفورم
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'amount'      => 'nullable|numeric|min:0',
        'days_count'  => 'nullable|integer|min:0',
        'description' => 'required|string|max:500',
        'date'        => 'required|date',
        'type'        => 'nullable|in:performance,achievement,other',
    ]);

    try {
        // نستخدم DB Transaction لضمان أن الحفظ وتحديث الرصيد والترحيل المالي يتمان معاً
        $reward = DB::transaction(function () use ($request) {

            // 2. إنشاء سجل المكافأة في جدول المكافآت الأصلي
            $newReward = Reward::create([
                'employee_id' => $request->employee_id,
                'amount'      => $request->amount ?? 0,
                'days_count'  => $request->days_count ?? 0,
                'description' => $request->description,
                'date'        => $request->date,
                'type'        => $request->type ?? 'other',
            ]);

            // [إضافة] 3. ترحيل المبلغ المالي إلى الحاوية المالية الموحدة
            if ($request->amount > 0) {
                \App\Models\FinancialTransaction::create([
                    'employee_id'      => $request->employee_id,
                    'type'             => 'bonus', // مكافأة (+)
                    'amount'           => $request->amount,
                    'description'      => "مكافأة أداء: " . $request->description,
                    'transaction_date' => $request->date,
                ]);
            }

            // 4. تحديث رصيد الإجازات في جدول الموظفين (إذا وجد أيام مكافأة)
            if ($request->days_count > 0) {
                $employee = Employee::findOrFail($request->employee_id);
                $employee->increment('total_leave_balance', $request->days_count);
            }

            return $newReward;
        });

        // 5. إرسال الإشعار للموظف
        $employee = Employee::with('user')->find($request->employee_id);
        if ($employee && $employee->user) {
            $employee->user->notify(new HRSystemNotification([
                'title' => 'مبروك! حصلت على مكافأة جديدة',
                'body'  => 'تم إضافة مكافأة بقيمة ' . number_format($request->amount, 2) . ' إلى سجلك المالي.',
                'type'  => 'reward',
                'icon'  => 'fas fa-gift',
                'color' => 'text-success',
                'link'  => route('admin.rewards.index'),
            ]));
        }

        return redirect()->route('admin.rewards.index')->with('success', 'تمت إضافة المكافأة، ترحيلها مالياً، وإشعار الموظف بنجاح.');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
    }
}
    public function create()
    {
        // جلب جميع الموظفين (يمكنك إضافة شرط الحالات النشطة فقط إذا أردت)
        $employees = Employee::all();

        // التأكد من إرسال المتغير بنفس الاسم المستخدم في ملف blade
        return view('admin.rewards.create', compact('employees'));
    }
    public function edit(Reward $reward)
    {
        return view('admin.rewards.edit', compact('reward'));
    }

    public function destroy(Reward $reward)
    {
        $reward->delete();
        return redirect()->back()->with('success', 'تم حذف سجل المكافأة بنجاح.');
    }
public function allNotifications()
{
    /** @var \App\Models\User $user */
    $user = auth()->user();

    // جلب كل الإشعارات (المقروءة وغير المقروءة) مع الترقيم الصفحي
    $notifications = $user->notifications()->latest()->paginate(10);

    return view('admin.notifications.index', compact('notifications'));
}
// داخل الكلاس RewardController

public function update(Request $request, $id)
{
    // 1. البحث عن السجل
    $reward = Reward::findOrFail($id);

    // 2. التحقق من البيانات المرسلة
    $request->validate([
        'amount' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ]);

    // 3. تحديث البيانات
    $reward->update([
        'amount'      => $request->amount,
        'description' => $request->description,
        // أيام الإجازة أيام قراءة فقط كما في ملف التعديل الخاص بك
    ]);

    // 4. العودة بصفحة النجاح
    return redirect()->route('admin.rewards.index')
                     ->with('success', 'تم تحديث بيانات المكافأة بنجاح');
}

public function getDashboardData()
    {
        try {
            // 1. الحصول على الموظف المرتبط بالمستخدم الحالي
            // ملاحظة: تأكد أن علاقة 'employee' معرفة في موديل User
            $user = Auth::user();
            $employee = $user->employee; 

            if (!$employee) {
                return response()->json(['message' => 'بيانات الموظف غير موجودة'], 404);
            }

            // 2. جلب المكافآت (Rewards) الخاصة بهذا الموظف
            // جلب أخر 10 مكافآت مرتبة من الأحدث
            $rewards = Reward::where('employee_id', $employee->id)
                             ->orderBy('date', 'desc')
                             ->get();

            // 3. تجميع الإحصائيات (تأكد من مطابقة أسماء الحقول في جدول الموظفين)
            $stats = [
                'total_leave_balance' => $employee->total_leave_balance ?? 0,
                'rewards_count'       => $rewards->count(),
                'total_rewards_sum'   => $rewards->sum('amount'),
            ];

            // 4. إرجاع الاستجابة النهائية
            return response()->json([
                'status' => 'success',
                'user' => [
                    'name' => $employee->name,
                    'job_title' => $employee->job_title ?? 'موظف',
                ],
                'rewards' => $rewards, // هذا الحقل الذي سنستخدمه في React Native
                'stats' => $stats,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في جلب البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

}
