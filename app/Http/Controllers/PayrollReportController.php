<?php

namespace App\Http\Controllers;

use App\Models\PayrollReport;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollReportController extends Controller
{
    /**
     * عرض التقرير النهائي للشركة
     */
public function index(Request $request)
{
    $month = $request->month ?? date('m');
    $year = $request->year ?? date('Y');
    $search = $request->search;

    $query = PayrollReport::with('employee')
        ->where('month', $month)
        ->where('year', $year);

    // البحث باسم الموظف
    if ($search) {
        $query->whereHas('employee', function ($q) use ($search) {
            $q->where('full_name', 'LIKE', "%{$search}%");
        });
    }

    $reports = $query->get();

    // 🟢 حساب الإجماليات للإدارة
    $summary = [
        'total_basic'     => $reports->sum('basic_salary'),
        'total_bonuses'   => $reports->sum('total_bonuses'),
        'total_deductions' => $reports->sum('total_deductions'),
        'total_loans'      => $reports->sum('loan_installment'),
        'total_assets'     => $reports->sum('held_assets'),
        'total_net'        => $reports->sum('net_salary'),
        'employee_count'   => $reports->count(),
    ];

    return view('admin.payroll_reports.index', compact('reports', 'month', 'year', 'search', 'summary'));
}

    /**
     * عملية "التجميع والترحيل" من شاشة الصرف إلى جدول التقارير
     */
    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'employee_id' => 'required',
            'month' => 'required',
            'year' => 'required',
            'net_salary' => 'required',
        ]);

        // 2. استخدام Transaction لضمان سلامة البيانات المالية
        DB::beginTransaction();
        try {
            // حفظ السجل في جدول التقرير النهائي
            PayrollReport::create([
                'employee_id'      => $request->employee_id,
                'month'            => $request->month,
                'year'             => $request->year,
                'basic_salary'     => $request->basic_salary,
                'total_bonuses'    => $request->total_bonuses,
                'total_deductions' => $request->total_deductions,
                'loan_installment' => $request->loan_installment,
                'held_assets'      => $request->held_assets,
                'net_salary'       => $request->net_salary,
                'payment_date'     => now(),
                'status'           => 'paid',
            ]);

            // هنا يمكنك إضافة كود لتحديث حالة السلف أو العهد إلى "تم الخصم"
            
            DB::commit();
            return redirect()->back()->with('success', 'تم اعتماد الراتب وترحيله للتقرير النهائي بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء الترحيل: ' . $e->getMessage());
        }
    }
    /**
     * عرض إيصال الصرف لموظف محدد
     */
    public function showReceipt($id)
    {
        // جلب بيانات الراتب من جدول التقارير الجديد مع بيانات الموظف
        // استخدمنا PayrollReport لأننا نريد البيانات المعتمدة نهائياً
        $payroll = PayrollReport::with(['employee.department'])->findOrFail($id);

        return view('admin.payroll_reports.receipt', compact('payroll'));
    }
    public function print(Request $request)
{
    $month = $request->month;
    $year = $request->year;
    
    // جلب البيانات بنفس الطريقة المستخدمة في الـ index
    $reports = PayrollReport::where('month', $month)->where('year', $year)->get();
    
    // حساب الإجماليات (الـ summary)
    $summary = [
        'total_basic' => $reports->sum('basic_salary'),
        'total_bonuses' => $reports->sum('total_bonuses'),
        'total_deductions' => $reports->sum('total_deductions'),
        'total_loans' => $reports->sum('loan_installment'),
        'total_assets' => $reports->sum('held_assets'),
        'total_net' => $reports->sum('net_salary'),
    ];

    // 🟢 التعديل هنا: أضف admin. قبل اسم المجلد
return view('admin.payroll_reports.print', compact('reports', 'summary', 'month', 'year'));
}
}
