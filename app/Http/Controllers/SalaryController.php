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
    // جلب الرواتب مع الموظفين المرتبطين بها فقط لإعداد التقرير الإجمالي
    $salaries = Salary::with('employee')->get();

    // متغيرات التقارير والإحصائيات الكلية في المنشأة
    $totalEmployeesCount = $salaries->count(); 
    $totalBasicSalaries = 0;
    $totalAllowances = 0;
    $totalNetSalaries = 0;

    foreach ($salaries as $salary) {
        // حساب الصافي الثابت (الأساسي + البدلات) للموظف الحالي
        $salary->net_salary = $salary->basic_salary + ($salary->allowances ?? 0);

        // تجميع التقارير الكلية للمنشأة
        $totalBasicSalaries += $salary->basic_salary;
        $totalAllowances += ($salary->allowances ?? 0);
        $totalNetSalaries += $salary->net_salary;
    }

    return view('admin.salaries.index', compact(
        'salaries', 
        'totalEmployeesCount', 
        'totalBasicSalaries', 
        'totalAllowances', 
        'totalNetSalaries'
    ));
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
        return redirect()->route('admin.salaries.index')->with('success', 'تم تحديث بيانات الراتب بنجاح');
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
