<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;




class LeaveController extends Controller
{
    public function index()
{
    // جلب الإجازات مع بيانات الموظفين (Eager Loading) وترتيبها بحيث يظهر المعلق أولاً
    $leaves = Leave::with('employee')
        ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
        ->latest()
        ->paginate(10);

    return view('admin.leaves.index', compact('leaves'));
}
    // 1. الموظف يرسل الطلب (Request)
   public function store(Request $request)
{
    // 1. التحقق من البيانات
    $request->validate([
        'leave_type' => 'required',
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // جلب الموظف المرتبط بالمستخدم المسجل دخول حالياً
    $employee = auth()->user()->employee;

    if (!$employee) {
        return response()->json(['message' => 'بيانات الموظف غير موجودة'], 404);
    }

    // 2. التحقق من تداخل التواريخ
    $exists = Leave::where('employee_id', $employee->id)
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
        })->exists();

    if ($exists) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'لديك طلب إجازة آخر يتداخل مع هذه التواريخ!'], 422);
        }
        return back()->with('error', 'لديك طلب إجازة آخر يتداخل مع هذه التواريخ!');
    }

    // 3. حساب عدد الأيام
    $start = Carbon::parse($request->start_date);
    $end = Carbon::parse($request->end_date);
    $daysCount = $start->diffInDays($end) + 1;

    // 4. تجهيز البيانات للحفظ
    $data = $request->all();
    $data['employee_id'] = $employee->id;
    $data['days_count'] = $daysCount;
    $data['status'] = 'pending'; // الحالة الافتراضية

    if ($request->hasFile('attachment')) {
        $data['attachment'] = $request->file('attachment')->store('leaves', 'public');
    }

    $leave = Leave::create($data);

    // 5. الرد بناءً على نوع الطلب (تطبيق أو متصفح)
    if ($request->wantsJson()) {
        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال طلب الإجازة بنجاح، بانتظار موافقة الإدارة.',
            'data' => $leave
        ], 201);
    }

    return back()->with('success', 'تم إرسال طلب الإجازة بنجاح، بانتظار موافقة الإدارة.');
}

    // 2. المدير يتخذ قراراً (Action)
    public function updateStatus(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update([
            'status' => $request->status, // approved or rejected
            'admin_reply' => $request->admin_reply
        ]);

        // ملاحظة: الخصم من الرصيد سيتم آلياً في الـ View عبر الـ Accessor الذي كتبناه في الموديل
        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
    public function getEvents()
{
    $leaves = Leave::with('employee')
        ->where('status', 'approved') // نعرض فقط الإجازات المقبولة على التقويم
        ->get();

    $events = $leaves->map(function ($leave) {
        return [
            'title' => $leave->employee->full_name . ' (' . $leave->leave_type . ')',
            'start' => $leave->start_date,
            'end'   => Carbon::parse($leave->end_date)->addDay()->toDateString(), // إضافة يوم لأن التقويم يحسب حتى بداية اليوم
            'backgroundColor' => $this->getColor($leave->leave_type),
            'borderColor'     => $this->getColor($leave->leave_type),
        ];
    });

    return response()->json($events);
}

// دالة بسيطة لتلوين الإجازات حسب نوعها
private function getColor($type) {
    return match($type) {
        'annual' => '#007bff', // أزرق
        'sick'   => '#ffc107',   // أصفر
        'unpaid' => '#dc3545', // أحمر
        default  => '#6c757d',
    };
}
public function myRequests() {
    $employee = auth()->user()->employee;
    return response()->json(
        Leave::where('employee_id', $employee->id)->latest()->get()
    );
}
}