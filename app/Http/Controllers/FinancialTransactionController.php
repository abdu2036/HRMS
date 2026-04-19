<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\Employee;
use Illuminate\Http\Request;

class FinancialTransactionController extends Controller
{
    // عرض سجل الحركات المالية الموحد
    public function index(Request $request)
    {
        $query = FinancialTransaction::with('employee');

        // فلترة حسب الموظف إذا تم الاختيار
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }

        // فلترة حسب نوع العملية
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest()->paginate(20);
        $employees = Employee::all();

        return view('admin.financial.index', compact('transactions', 'employees'));
    }

    // حذف حركة مالية (في حال الخطأ)
    public function destroy($id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $transaction->delete();

        return redirect()->back()->with('success', 'تم حذف القيد المالي بنجاح، سيتم تحديث صافي الراتب تلقائياً.');
    }
}