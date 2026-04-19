<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketAllowance;
use App\Models\TicketTransaction;
use Illuminate\Support\Facades\DB;

class TicketTransactionController extends Controller
{
   public function store(Request $request)
{
    $request->validate([
        'amount_spent' => 'required|integer|min:1',
        'notes' => 'nullable|string|max:255',
    ]);

    $employee = auth()->user()->employee;

    // 1. جلب سجل التذاكر للشهر الحالي
    $allowance = TicketAllowance::where('employee_id', $employee->id)
        ->where('month', now()->month)
        ->where('year', now()->year)
        ->first();

    // 2. التحقق من وجود رصيد كافٍ
    if (!$allowance || $allowance->current_balance < $request->amount_spent) {
        $msg = 'عذراً، رصيدك الحالي لا يكفي لإتمام هذا الطلب.';
        return $request->expectsJson() 
            ? response()->json(['message' => $msg], 400) 
            : back()->with('error', $msg);
    }

    try {
        DB::transaction(function () use ($request, $employee, $allowance) {
            TicketTransaction::create([
                'employee_id'   => $employee->id,
                'amount_spent'  => $request->amount_spent,
                'reference_code' => 'TKT-' . strtoupper(uniqid()),
                'spent_at'      => now(),
                'notes'         => $request->notes,
            ]);

            $allowance->decrement('current_balance', $request->amount_spent);
        });

        $successMsg = 'تم تسجيل طلب صرف التذاكر بنجاح.';
        return $request->expectsJson() 
            ? response()->json(['message' => $successMsg], 200) 
            : back()->with('success', $successMsg);

    } catch (\Exception $e) {
        return $request->expectsJson() 
            ? response()->json(['message' => 'حدث خطأ في الخادم'], 500) 
            : back()->with('error', 'حدث خطأ ما');
    }
}

    // دالة جديدة لجلب رصيد الشهر والسجل للموبايل

}