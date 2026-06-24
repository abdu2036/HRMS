<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollReport; // 🟢 استدعاء الموديل الجديد
use App\Models\FinancialTransaction;
use App\Models\Loan;
use App\Models\Custody;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Notifications\HRSystemNotification;

class PayrollController extends Controller
{
public function index(Request $request)
{
    // 1. استلام قيم الفلتر
    $month = $request->month ?? Carbon::now()->month;
    $year = $request->year ?? Carbon::now()->year;
    $search = $request->search;

    // 2. بناء الاستعلام الأساسي
    $query = Employee::query();

    // 🟢 استبعاد الموظفين غير النشطين لضمان عدم ظهورهم نهائياً في الصرف
    $query->where('status', '!=', 'غير نشط')
          ->where('status', '!=', 'inactive');

    if ($request->filled('search')) {
        $query->where('full_name', 'LIKE', "%{$search}%");
    }

    // 🟢 الترتيب من أول موظف تم إدراجه (الأقدم) إلى آخر موظف (الأحدث)
    $query->orderBy('id', 'asc');

    // 3. جلب الموظفين مع علاقة المرتب الأساسي (salary) وفحص جدول التقارير لظهور "تم الاعتماد"
    $employees = $query->with(['salary', 'payrollReports' => function ($q) use ($month, $year) {
        $q->where('month', $month)->where('year', $year);
    }])->get();

    foreach ($employees as $emp) {
        // نتحقق من وجود سجل في جدول التقارير الجديد (PayrollReport)
        $emp->payroll_record = $emp->payrollReports->first();

        if ($emp->payroll_record) {
            // إذا وجد سجل، نستخدم القيم المخزنة فيه مباشرة
            $emp->basic_salary = round($emp->payroll_record->basic_salary, 2);
            $emp->total_bonuses = round($emp->payroll_record->total_bonuses, 2);
            $emp->total_deductions = round($emp->payroll_record->total_deductions, 2);
            $emp->loan_installment = round($emp->payroll_record->loan_installment, 2);
            $emp->held_amount = round($emp->payroll_record->held_assets, 2);
            $emp->deferred_from_previous = 0;
            $emp->payroll_status = true; // 🟢 يجعل الزر يتغير لـ "تم الاعتماد"
        } else {
            // 🟢 الحل: جلب المرتب الأساسي من جدول العلاقة الحقيقي (salaries)
            $db_basic_salary = $emp->salary ? $emp->salary->basic_salary : $emp->basic_salary;
            $emp->basic_salary = round((float) $db_basic_salary, 2);

            // حسابات الحركات المعلقة مع التقريب الثنائي الحاسم لمنع كسور الـ Float
            $emp->total_bonuses = round(FinancialTransaction::where('employee_id', $emp->id)
                ->where('type', 'bonus')->where('status', 'pending')
                ->whereMonth('transaction_date', $month)->whereYear('transaction_date', $year)->sum('amount'), 2);

            $emp->total_deductions = round(FinancialTransaction::where('employee_id', $emp->id)
                ->whereIn('type', ['penalty', 'absent'])->where('status', 'pending')
                ->whereMonth('transaction_date', $month)->whereYear('transaction_date', $year)->sum('amount'), 2);

            // حساب مجموع أقساط كافة السلف النشطة
            $emp->loan_installment = round(Loan::where('employee_id', $emp->id)
                ->where('status', 'active')
                ->where('remaining_amount', '>', 0)
                ->sum('installment'), 2); 

            $emp->held_amount = round(Custody::where('employee_id', $emp->id)
                ->whereMonth('created_at', $month)->whereYear('created_at', $year)
                ->whereIn('status', ['received', 'shortage'])->sum('amount'), 2);

            $emp->deferred_from_previous = round(Custody::where('employee_id', $emp->id)
                ->where('status', 'deferred')
                ->where(function ($q) use ($month, $year) {
                    $q->whereYear('created_at', '<', $year)
                        ->orWhere(function ($sq) use ($month, $year) {
                            $sq->whereYear('created_at', $year)->whereMonth('created_at', '<', $month);
                        });
                })->sum('amount'), 2);

            $emp->payroll_status = false;
        }
        $emp->total_custody_to_deduct = round($emp->held_amount + $emp->deferred_from_previous, 2);
    }

    return view('admin.payroll.index', compact('employees', 'month', 'year'));
}
   public function process(Request $request)
{
    $request->validate([
        'employee_id' => 'required',
        'net_salary' => 'required|numeric',
        'month' => 'required',
        'year' => 'required',
        'basic_salary' => 'required|numeric',
        'payment_method' => 'required|string|in:cash,bank_transfer',
    ]);

    try {
        // نضع العملية بالكامل في متغير لاستخدامها في الإشعار
        $report = DB::transaction(function () use ($request) {

            // 1. تسجيل الراتب في جدول التقارير الجديد (PayrollReport)
            $newReport = PayrollReport::create([
                'employee_id'      => $request->employee_id,
                'month'            => $request->month,
                'year'             => $request->year,
                'basic_salary'     => $request->basic_salary,
                'total_bonuses'    => $request->total_bonuses ?? 0,
                'total_deductions' => $request->total_deductions ?? 0,
                'loan_installment' => $request->loan_installment ?? 0,
                'held_assets'      => $request->held_assets ?? 0,
                'net_salary'       => $request->net_salary,
                'payment_date'     => now(),
                'status'           => 'paid',
                'payment_method'   => $request->payment_method,
            ]);

            // 2. تصفية الحركات المالية
            FinancialTransaction::where('employee_id', $request->employee_id)
                ->where('status', 'pending')
                ->whereMonth('transaction_date', $request->month)
                ->whereYear('transaction_date', $request->year)
                ->update(['status' => 'paid']);

            // 3. تحديث السلف
            $loan = Loan::where('employee_id', $request->employee_id)
                ->where('status', 'active')
                ->where('remaining_amount', '>', 0)
                ->first();

            if ($loan) {
                $installment = $request->loan_installment ?? $loan->installment;
                $newRemaining = $loan->remaining_amount - $installment;
                $loan->update([
                    'remaining_amount' => max(0, $newRemaining),
                    'status' => ($newRemaining <= 0) ? 'paid' : 'active'
                ]);
            }

            // 4. تصفية العهد
            if ($request->held_assets > 0) {
                Custody::where('employee_id', $request->employee_id)
                    ->whereIn('status', ['received', 'shortage', 'deferred'])
                    ->where(function ($query) use ($request) {
                        $query->whereMonth('created_at', '<=', $request->month)
                            ->whereYear('created_at', '<=', $request->year);
                    })
                    ->update(['status' => 'settled']);
            }

            return $newReport;
        });

        // 🟢 5. إرسال الإشعار للموظف بعد نجاح المعاملة
        $employee = Employee::with('user')->find($request->employee_id);
        if ($employee && $employee->user) {
            $employee->user->notify(new HRSystemNotification([
                'title' => 'تم صرف الراتب 💰',
                'body'  => "تم اعتماد صرف راتب شهر {$request->month}/{$request->year}. المبلغ الصافي: " . number_format($request->net_salary, 2) . " د.ل. شكراً لجهودكم.",
                'type'  => 'salary',
                'icon'  => 'fas fa-money-bill-wave',
                'color' => 'text-success',
                'link'  => '#' // يمكنك وضع رابط لصفحة كشف الراتب هنا
            ]));
        }

        return redirect()->back()->with('success', 'تم اعتماد الراتب بنجاح وتصفية العهد وإشعار الموظف ✅');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ أثناء المعالجة: ' . $e->getMessage());
    }
}
    public function showReceipt($id)
{
    // جلب بيانات الراتب المعتمدة من الجدول الجديد
    $payroll = PayrollReport::with(['employee.department'])->findOrFail($id);

    return view('admin.payroll.receipt', compact('payroll'));
}
}