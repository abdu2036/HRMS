<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Employee;
use App\Models\FinancialTransaction;


class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    // جلب جميع الموظفين المسجلين في جدول الموظفين وليس جدول المستخدمين
    $allEmployees = Employee::all();
    // 1. جلب الرواتب التي تملك موظفاً فقط لتجنب خطأ Attempt to read property on null
    // مع جلب العمليات المالية المعلقة (pending) فقط في استعلام واحد (Eager Loading)
    $salaries = Salary::with('employee')
        ->with(['employee.financialTransactions' => function($query) {
            $query->where('status', 'pending');
        }])
        ->get();

    foreach ($salaries as $salary) {
        $employee = $salary->employee;

        // التحقق الإضافي لزيادة الأمان (رغم وجود has('employee'))
        if ($employee) {
            // 2. حساب المكافآت المعلقة (+) باستخدام العلاقة المحملة مسبقاً
            $totalBonuses = $employee->financialTransactions
                ->where('type', 'bonus')
                ->sum('amount');

            // 3. حساب الخصومات المعلقة (-) (سلف، عجز عهدة، جزاءات)
            $totalDeductions = $employee->financialTransactions
                ->whereIn('type', ['penalty', 'custody_deficit', 'advance'])
                ->sum('amount');

            // 4. تخزين القيم داخل كائن الراتب لاستخدامها في الـ Blade
            $salary->total_bonuses = $totalBonuses;
            $salary->total_deductions = $totalDeductions;

            // 5. حساب الصافي النهائي (الراتب الأساسي + البدلات + المكافآت - الخصومات)
            $salary->net_salary = ($salary->basic_salary + ($salary->allowances ?? 0) + $totalBonuses) - $totalDeductions;
        } else {
            // في حالة نادرة (إذا لم يوجد موظف) نضع قيماً افتراضية
            $salary->total_bonuses = 0;
            $salary->total_deductions = 0;
            $salary->net_salary = $salary->basic_salary + ($salary->allowances ?? 0);
        }
    }

    return view('admin.salaries.index', compact('salaries'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:salaries,employee_id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        Salary::create($request->all());

        return redirect()->route('salaries.index')->with('success', 'تم تحديد راتب الموظف بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. البحث عن السجل أو إرجاع خطأ 404 إذا لم يوجد
        $salary = Salary::findOrFail($id);

        // 2. التحقق من البيانات (نفس شروط الإضافة باستثناء الموظف لأنه ثابت)
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        // 3. تحديث البيانات
        $salary->update($request->all());

        // 4. الرد وإعادة التوجيه
        return redirect()->route('salaries.index')->with('success', 'تم تحديث بيانات الراتب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // البحث عن السجل
        $salary = Salary::findOrFail($id);

        // الحذف الفعلي
        $salary->delete();

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('salaries.index')->with('success', 'تم حذف سجل الراتب بنجاح');
    }
    public function approvePayment(Request $request, $id)
{
    // 1. البحث عن سجل راتب الموظف
    $salary = Salary::findOrFail($id);

    // 2. تحويل كافة العمليات المالية "المعلقة" لهذا الموظف إلى "مدفوعة"
    // هذا السطر هو الذي سيمنع الـ 100 دينار من الظهور في شهر 4
    $salary->employee->financialTransactions()
        ->where('status', 'pending')
        ->update(['status' => 'paid']);

    // 3. (اختياري) يمكنك هنا إضافة سجل في جدول "الرواتب المصروفة" (Payroll) لتوثيق العملية

    return redirect()->back()->with('success', 'تم اعتماد وصرف الراتب وتصفية كافة الحسابات بنجاح.');
}
}
