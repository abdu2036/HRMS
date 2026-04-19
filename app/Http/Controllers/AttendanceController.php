<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Penalty;
use App\Models\EarlyExitPermission; // 🟢 استدعاء الموديل الجديد

class AttendanceController extends Controller
{
    /**
     * عرض لوحة تحكم الموظف (Dashboard)
     */
// دالة الموظف (لوحة تحكم الموظف - الصورة image_95083f.png)
public function index()
{
    // 1. جلب الـ employee_id من حساب المستخدم المسجل حالياً
    $employeeId = auth()->user()->employee_id;

    // 2. إذا كان المستخدم (مدير مثلاً) وليس لديه employee_id، نعيده للخلف
    if (!$employeeId) {
        return redirect()->route('dashboard')->with('error', 'هذا الحساب غير مرتبط بملف موظف.');
    }

    // 3. جلب بيانات الموظف مع الفروع والورديات
    $employee = Employee::with(['branch', 'shift'])->find($employeeId);

    // 4. التأكد من وجود الموظف في جدول employees
    if (!$employee) {
        return redirect()->route('dashboard')->with('error', 'تعذر العثور على بيانات الموظف.');
    }

    // 5. جلب سجل حضور اليوم
    $attendance = Attendance::where('employee_id', $employee->id)
                            ->where('date', now()->toDateString())
                            ->first();

    return view('attendances.checkin', compact('attendance', 'employee'));
}

    /**
     * تسجيل الحضور (Check-in)
     */
public function store(Request $request)
{
    // 1. التحقق من المدخلات
    $request->validate([
        'lat' => 'required|numeric',
        'lng' => 'required|numeric',
    ]);

    $employeeId = auth()->user()->employee_id;

    if (!$employeeId) {
        return $request->expectsJson() 
            ? response()->json(['error' => 'هذا الحساب غير مرتبط بملف موظف.'], 404) 
            : back()->with('error', 'هذا الحساب غير مرتبط بملف موظف.');
    }

    $employee = Employee::with(['branch', 'shift'])->find($employeeId);

    if (!$employee || !$employee->branch || !$employee->shift) { 
        $msg = 'بيانات الموظف أو الفرع أو الوردية غير مكتملة.';
        return $request->expectsJson() 
            ? response()->json(['error' => $msg], 400) 
            : back()->with('error', $msg);
    }

    // حساب المسافة
    $distance = $this->calculateDistance(
        $request->lat, $request->lng,
        $employee->branch->latitude, $employee->branch->longitude
    );

    $allowedRadius = $employee->branch->radius_meters ?? 200;
    if ($distance > $allowedRadius) {
        $msg = "أنت خارج النطاق المسموح! المسافة: " . round($distance) . " متر.";
        return $request->expectsJson() 
            ? response()->json(['error' => $msg], 403) 
            : back()->with('error', $msg);
    }

    $today = now()->toDateString();
    $attendance = Attendance::where('employee_id', $employee->id)
                            ->where('date', $today)
                            ->first();

    if (!$attendance) {
        $now = now();
        $startTime = Carbon::createFromFormat('H:i:s', $employee->shift->start_time);
        $gracePeriod = 10; 
        
        $lateMinutes = 0;
        if ($now->greaterThan($startTime)) {
            $diff = $now->diffInMinutes($startTime);
            if ($diff > $gracePeriod) {
                $lateMinutes = $diff;
            }
        }

        // الحفظ الفعلي
        Attendance::create([
            'employee_id'          => $employee->id,
            'date'                 => $today,
            'signin_time'          => $now->toTimeString(),
            'status'               => $lateMinutes > 0 ? 'late' : 'present',
            'late_minutes'         => $lateMinutes,
            'lat'                  => $request->lat,
            'lng'                  => $request->lng,
            'distance_from_branch' => round($distance, 2),
        ]);

        $successMsg = 'تم تسجيل حضورك بنجاح في قاعدة البيانات.';
        return $request->expectsJson() 
            ? response()->json(['message' => $successMsg], 201) 
            : back()->with('success', $successMsg);
    }

    $infoMsg = 'لقد سجلت حضورك مسبقاً اليوم.';
    return $request->expectsJson() 
        ? response()->json(['message' => $infoMsg], 200) 
        : back()->with('info', $infoMsg);
}

    /**
     * تسجيل الانصراف (Check-out)
     */
/**
 * تسجيل الانصراف (Check-out) - معدلة لتشمل أذونات الخروج
 */
public function update(Request $request, $id)
{
    $attendance = Attendance::findOrFail($id);
    $employee = Employee::with('shift')->find($attendance->employee_id);

    if ($attendance->signout_time) {
        return $request->expectsJson() 
            ? response()->json(['message' => 'لقد سجلت انصرافك مسبقاً.'], 200)
            : back()->with('info', 'لقد سجلت انصرافك مسبقاً.');
    }

    $now = now();
    
    // 1. جلب وقت نهاية الشفت الأصلي
    $shiftEndTime = Carbon::parse($employee->shift->end_time);

    // 2. البحث عن إذن خروج لهذا اليوم (الجدول الجديد الذي أنشأناه)
    // ملاحظة: تأكد من عمل import لموديل EarlyExitPermission في أعلى الملف
    $permission = EarlyExitPermission::where('employee_id', $employee->id)
                    ->where('date', now()->toDateString())
                    ->first();

    // 3. تحديد "وقت النهاية المعتمد"
    // إذا وجد إذن، نعتبر وقت الإذن هو نهاية الدوام، وإلا نعتمد وقت الشفت الأصلي
    $effectiveEndTime = $permission ? Carbon::parse($permission->allowed_exit_time) : $shiftEndTime;

    $overtimeMinutes = 0;
    $earlyOutMinutes = 0;

    // 4. الحسابات بناءً على الوقت المعتمد (Effective End Time)
    if ($now->greaterThan($shiftEndTime)) {
        // الإضافي دائماً يحسب بناءً على الشفت الأصلي (حتى لو خرج بإذن، الإضافي يبدأ بعد نهاية الدوام الرسمي)
        $overtimeMinutes = $now->diffInMinutes($shiftEndTime);
    } 
    
    if ($now->lessThan($effectiveEndTime)) {
        // الموظف خرج قبل الوقت المسموح به (سواء الإذن أو الشفت)
        $earlyOutMinutes = $now->diffInMinutes($effectiveEndTime);
    }

    // 5. التحديث في قاعدة البيانات
    $attendance->update([
        'signout_time'      => $now->toTimeString(),
        'overtime_minutes'  => $overtimeMinutes,
        'early_out_minutes' => $earlyOutMinutes,
    ]);

    // 6. تجهيز الرسالة
    $message = 'تم تسجيل الانصراف بنجاح.';
    if ($permission) $message .= " (تم اعتماد إذن خروج مبكر)";
    if ($overtimeMinutes > 0) $message .= " لديك $overtimeMinutes دقيقة عمل إضافي.";
    if ($earlyOutMinutes > 0) $message .= " ملاحظة: تم تسجيل انصراف مبكر بـ $earlyOutMinutes دقيقة.";

    return $request->expectsJson() 
        ? response()->json(['message' => $message, 'status' => 'success'], 200)
        : back()->with('success', $message);
}

    /**
     * دالة رياضية لحساب المسافة بين نقطتين (Haversine Formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) 
    {
        $earthRadius = 6371000; // بالامتار

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c; 
    }
   // داخل AttendanceController.php

     
// دالة المدير (سجلات الحضور العامة)
public function adminIndex(Request $request)
{
    $query = Attendance::with(['employee.branch', 'employee.department']);
    
    if ($request->date) {
        $query->whereDate('date', $request->date);
    } else {
        $query->whereDate('date', now()->toDateString());
    }

    $attendances = $query->latest()->get(); 

    // التوجيه لملف الإدارة (داخل مجلد admin)
    return view('admin.attendances.index', compact('attendances'));
}

public function generateReport(Request $request)
{
    $month = $request->query('month', now()->format('Y-m'));
    $employeeId = $request->query('employee_id');

    $attendances = Attendance::where('employee_id', $employeeId)
        ->where('date', 'like', "$month%")
        ->orderBy('date', 'asc')
        ->get();

    $employee = Employee::findOrFail($employeeId);

    // حساب الإجماليات للتقرير
    $totalLate = $attendances->sum('late_minutes');
    $totalEarlyOut = $attendances->sum('early_out_minutes');

    $data = [
        'title' => 'تقرير الحضور والانصراف الشهري',
        'date' => $month,
        'employee' => $employee,
        'attendances' => $attendances,
        'totalLate' => $totalLate,
        'totalEarlyOut' => $totalEarlyOut,
    ];

    // يمكنك استخدام view عادية للطباعة من المتصفح أو DomPDF لتحويلها لملف
    return view('admin.attendances.pdf_report', $data);
}
public function exportExcel(Request $request)
{
    $employeeId = $request->query('employee_id');
    $month = $request->query('month', now()->format('Y-m'));

    if (!$employeeId) {
        return back()->with('error', 'يرجى اختيار موظف أولاً للتصدير.');
    }

    return Excel::download(new AttendanceExport($employeeId, $month), 'report_'.$month.'.xlsx');
}

// دالة ملخص شهري لكل الموظفين (تقرير شامل)
public function monthlySummary(Request $request)
{
    $month = $request->get('month', now()->format('Y-m'));
    
    // جلب كل الموظفين مع سجلات حضورهم لهذا الشهر فقط
    $employees = Employee::with(['attendances' => function($query) use ($month) {
        $query->where('date', 'like', $month . '%');
    }])->get();

    $summary = $employees->map(function($employee) use ($month) {
        $daysInMonth = 30; 
        $workDays = $employee->attendances->count(); 
        
        return [
            'id'           => $employee->id, // <--- هذا السطر هو مفتاح الحل
            'name'         => $employee->full_name,
            'basic_salary' => $employee->basic_salary, 
            'total_late'   => $employee->attendances->sum('late_minutes'), 
            'total_early'  => $employee->attendances->sum('early_out_minutes'), 
            'present_days' => $workDays,
            'absent_days'  => max(0, $daysInMonth - $workDays), 
            'total_work_hours' => round($employee->attendances->sum(function($att) {
                if($att->signin_time && $att->signout_time) {
                    return \Carbon\Carbon::parse($att->signin_time)->diffInMinutes(\Carbon\Carbon::parse($att->signout_time)) / 60;
                }
                return 0;
            }), 2)
        ];
    });

    return view('admin.attendances.monthly_summary', compact('summary', 'month'));
}
public function updateFromMobile(Request $request)
{
    // الحصول على ID الموظف المرتبط بالمستخدم المسجل حالياً
    $employeeId = auth()->user()->employee_id; 

    // البحث عن سجل بصمة "حضور" لهذا اليوم ولم يتم إغلاقه بـ "انصراف"
    $attendance = Attendance::where('employee_id', $employeeId)
                            ->where('date', now()->toDateString())
                            ->whereNull('signout_time')
                            ->first();

    if (!$attendance) {
        return response()->json(['error' => 'لا يوجد سجل حضور مفتوح لهذا اليوم.'], 404);
    }

    // تمرير الطلب لدالة الـ update الأصلية لديك لمعالجة إحداثيات الموقع والوقت
    return $this->update($request, $attendance->id);
}
public function getStatus() {
    $employeeId = auth()->user()->employee_id;
    $today = now()->toDateString();
    
    $attendance = Attendance::where('employee_id', $employeeId)
                            ->where('date', $today)
                            ->first();

    return response()->json([
        'hasCheckedIn' => ($attendance && !$attendance->signout_time), // سجل حضور ولم ينصرف
        'isDayCompleted' => ($attendance && $attendance->signout_time), // بصم انصرافه
        'signinTime' => $attendance ? $attendance->signin_time : null
    ]);
}

/**
 * حفظ إذن الخروج المبكر من قبل المدير
 */
public function storeEarlyExit(Request $request)
{
    // 1. التحقق من صحة البيانات المرسلة من الـ Modal
    $request->validate([
        'employee_id'       => 'required|exists:employees,id',
        'date'              => 'required|date',
        'allowed_exit_time' => 'required',
        'reason'            => 'nullable|string|max:255',
    ]);

    // 2. حفظ الإذن في الجدول الجديد
    // ملاحظة: نستخدم updateOrCreate لتجنب تكرار الأذونات لنفس الموظف في نفس اليوم
    EarlyExitPermission::updateOrCreate(
        [
            'employee_id' => $request->employee_id,
            'date'        => $request->date,
        ],
        [
            'allowed_exit_time' => $request->allowed_exit_time,
            'reason'            => $request->reason,
            'created_by'        => auth()->id(), // تسجيل من المدير الذي قام بالإجراء
        ]
    );

    // 3. (اختياري ولكن مهم) تحديث سجل الحضور إذا كان الموظف قد بصم انصراف بالفعل
    // لكي يتغير لونه من أحمر (تبكير) إلى أخضر (بإذن) فوراً في الجدول
    $attendance = Attendance::where('employee_id', $request->employee_id)
                            ->where('date', $request->date)
                            ->first();

    if ($attendance && $attendance->signout_time) {
        // نستدعي دالة التحديث يدوياً لإعادة الحساب بناءً على الإذن الجديد
        $this->recalculateAttendance($attendance);
    }

    return back()->with('success', 'تم تسجيل إذن الخروج بنجاح وتحديث السجلات.');
}

/**
 * دالة مساعدة لإعادة حساب الدقائق بعد إضافة الإذن
 */
private function recalculateAttendance($attendance)
{
    $employee = Employee::with('shift')->find($attendance->employee_id);
    $shiftEndTime = Carbon::parse($employee->shift->end_time);
    $signoutTime = Carbon::parse($attendance->signout_time);

    // جلب الإذن الذي أضفناه للتو
    $permission = EarlyExitPermission::where('employee_id', $attendance->employee_id)
                    ->where('date', $attendance->date)
                    ->first();

    $effectiveEndTime = $permission ? Carbon::parse($permission->allowed_exit_time) : $shiftEndTime;

    $earlyOutMinutes = 0;
    if ($signoutTime->lessThan($effectiveEndTime)) {
        $earlyOutMinutes = $signoutTime->diffInMinutes($effectiveEndTime);
    }

    $attendance->update([
        'early_out_minutes' => $earlyOutMinutes
    ]);
}

public function transferMonthlyDeductions(Request $request)
{
    $month = $request->month; // يتوقع تنسيق 2026-04
    $daysInMonth = 30; // القاعدة المالية الثابتة

    // جلب جميع الموظفين الذين لديهم راتب أساسي (لأن بدونه لن نحسب خصم)
    $employees = Employee::where('basic_salary', '>', 0)->get();

    if ($employees->isEmpty()) {
        return back()->with('error', 'لا يوجد موظفون برواتب مسجلة لبدء الترحيل.');
    }

    DB::beginTransaction();
    try {
        $transferredCount = 0;

        foreach ($employees as $employee) {
            // 1. حساب قيمة اليوم والدقيقة
            $dailyRate = $employee->basic_salary / $daysInMonth;
            $minuteRate = ($dailyRate / 8) / 60;

            // 2. جلب سجلات الحضور الفعلي لهذا الشهر
            $attendances = Attendance::where('employee_id', $employee->id)
                                    ->where('date', 'like', "$month%")
                                    ->get();

            // 3. حساب الحضور والغياب (المنطق الصحيح)
            $presentDaysCount = $attendances->count();
            $absentDays = max(0, $daysInMonth - $presentDaysCount);
            $totalLateMinutes = $attendances->sum('late_minutes');

            // 4. حساب المبالغ
            $lateDeduction = $totalLateMinutes * $minuteRate;
            $absentDeduction = $absentDays * $dailyRate;
            $totalAmount = $lateDeduction + $absentDeduction;

            // 5. الترحيل فقط إذا كان هناك مبلغ خصم
            if ($totalAmount > 0) {
                // منع التكرار: لا ترحل لنفس الموظف ونفس الشهر مرتين
                $alreadyTransferred = Penalty::where('employee_id', $employee->id)
                    ->where('description', 'like', "%$month%")
                    ->exists();

                if (!$alreadyTransferred) {
                    // إدراج في جدول الجزاءات (العقوبات)
                    Penalty::create([
                        'employee_id' => $employee->id,
                        'amount'      => $totalAmount,
                        'days_count'  => $absentDays,
                        'date'        => now(),
                        'description' => "خصم آلي شهر ($month): غياب $absentDays يوم + تأخير $totalLateMinutes دقيقة",
                        'type'        => 'deduction' 
                    ]);

                    // إدراج في الحاوية المالية (اختياري حسب نظامك)
                    if (class_exists('\App\Models\FinancialTransaction')) {
                        \App\Models\FinancialTransaction::create([
                            'employee_id'      => $employee->id,
                            'amount'           => $totalAmount,
                            'type'             => 'penalty',
                            'description'      => "خصم حضور وانصراف $month",
                            'transaction_date' => now()
                        ]);
                    }
                    $transferredCount++;
                }
            }
        }

        DB::commit();

        if ($transferredCount > 0) {
            return back()->with('success', "تم بنجاح ترحيل خصومات ($transferredCount) موظف إلى سجل الجزاءات.");
        } else {
            return back()->with('info', 'لم يتم ترحيل أي بيانات؛ ربما تم ترحيلها مسبقاً أو لا توجد خصومات مستحقة.');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        // إظهار الخطأ الحقيقي للمبرمج لتعرف ماذا حدث
        return back()->with('error', 'حدث خطأ تقني: ' . $e->getMessage());
    }
}
}