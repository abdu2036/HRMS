<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penalty;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Notifications\HRSystemNotification;


class PenaltyController extends Controller
{
    public function index()
    {
        $penalties = Penalty::with('employee')->latest()->get();
        return view('admin.penalties.index', compact('penalties'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('admin.penalties.create', compact('employees'));
    }

   public function store(Request $request)
{
    // 1. التحقق من البيانات
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'description' => 'required|string',
        'date'        => 'required|date',
        'amount'      => 'nullable|numeric|min:0',
        'days_count'  => 'nullable|integer|min:0',
    ]);

    try {
        $penalty = DB::transaction(function () use ($request) {
            // 2. معالجة القيم وتجهيز البيانات
            $data = $request->all();
            $data['amount'] = $request->amount ?? 0;
            $data['days_count'] = $request->days_count ?? 0;

            // إنشاء سجل العقوبة في جدولها الأصلي
            $newPenalty = Penalty::create($data);

            // [إضافة] 3. ترحيل المبلغ المالي إلى الحاوية المالية الموحدة
            if ($data['amount'] > 0) {
                \App\Models\FinancialTransaction::create([
                    'employee_id'      => $request->employee_id,
                    'type'             => 'penalty', // جزاء (-)
                    'amount'           => $data['amount'],
                    'description'      => "جزاء إداري: " . $request->description,
                    'transaction_date' => $request->date,
                ]);
            }

            // 4. خصم رصيد الإجازات إذا وجد عدد أيام
            if ($data['days_count'] > 0) {
                $employee = Employee::findOrFail($request->employee_id);
                $employee->decrement('total_leave_balance', $data['days_count']);
            }

            return $newPenalty;
        });

        // 5. إرسال الإشعار الموحد
        $employee = Employee::with('user')->find($request->employee_id);
        if ($employee && $employee->user) {
            $employee->user->notify(new HRSystemNotification([
                'title' => 'تنبيه: تم تسجيل جزاء بحقك',
                'body'  => "تم تسجيل جزاء بسبب: {$penalty->description}. سيتم خصم مبلغ " . number_format($penalty->amount, 2) . " من الراتب و {$penalty->days_count} أيام من الرصيد.",
                'type'  => 'penalty',
                'icon'  => 'fas fa-gavel',
                'color' => 'text-danger',
                'link'  => route('admin.penalties.index')
            ]));
        }

        return redirect()->route('admin.penalties.index')->with('success', 'تم تسجيل العقوبة، خصمها مالياً، وإشعار الموظف بنجاح.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ أثناء الحفظ والترحيل المالي: ' . $e->getMessage());
    }
}

    public function edit(Penalty $penalty)
    {
        $employees = Employee::all();
        return view('admin.penalties.edit', compact('penalty', 'employees'));
    }

    public function update(Request $request, Penalty $penalty)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'description' => 'required',
            'date'        => 'required|date',
            'amount'      => 'nullable|numeric',
            'days_count'  => 'nullable|integer',
        ]);

        // ملاحظة: التعديل هنا لا يغير رصيد الإجازات (هذا تصرف صحيح لتجنب التضارب الحسابي)
        $data = $request->all();
        $data['amount'] = $request->amount ?? 0;
        $data['days_count'] = $request->days_count ?? 0;

        $penalty->update($data);

        return redirect()->route('admin.penalties.index')->with('success', 'تم تحديث بيانات الجزاء بنجاح.');
    }

    public function destroy(Penalty $penalty)
    {
        $penalty->delete();
        return redirect()->route('admin.penalties.index')->with('success', 'تم حذف سجل الجزاء بنجاح.');
    }
public function allNotifications()
{
    /** @var \App\Models\User $user */
    $user = auth()->user();

    // جلب كل الإشعارات (المقروءة وغير المقروءة) مع الترقيم الصفحي
    $notifications = $user->notifications()->latest()->paginate(10);

    return view('admin.notifications.index', compact('notifications'));
}

//


// داخل كلاس PenaltyController
public function readNotification($id)
{
    /** @var \App\Models\User $user */
    // الحصول على المستخدم الحالي
    $user = auth()->user();

    // البحث عن الإشعار من خلال علاقة الإشعارات التابعة للمستخدم
    $notification = $user->unreadNotifications()->find($id);

    if ($notification) {
        $notification->markAsRead();
    }

    return redirect()->back()->with('success', 'تم تحديث الإشعار');
}


// داخل PenaltyController.php

// أضف هذه الدالة لجلب البيانات بصيغة JSON للموبايل
public function getMyPenaltiesApi()
{
    try {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'الموظف غير موجود'], 404);
        }

        // جلب العقوبات من جدول penalties المربوط بموديل Penalty
        $penalties = Penalty::where('employee_id', $employee->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $penalties
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

}