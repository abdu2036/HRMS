<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Employee;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * عرض قائمة السلف
     */
    public function index()
    {
        // جلب السلف مع بيانات الموظف المرتبطة بجدول employees مباشرة
        $loans = Loan::with('employee')->latest()->get();
        
        // جلب جميع الموظفين (سيظهر الجميع الآن لأن القيد أصبح على جدول الموظفين)
        $employees = Employee::all(); 
        
        // حساب إجمالي المبالغ المتبقية للسلف النشطة
        $totalPendingLoans = Loan::where('status', 'active')->sum('remaining_amount');
        

        return view('admin.loans.index', compact('loans', 'employees', 'totalPendingLoans'));
    }

    /**
     * تخزين طلب سلفة جديد
     */
public function store(Request $request)
{
    // 1. التحقق من البيانات
    // لاحظ حذفنا employee_id من التحقق لأننا سنجلبه من المستخدم المسجل
    $rules = [
        'amount'      => 'required|numeric|min:1',
        'installment' => 'required|numeric|min:1|max:'.$request->amount,
        'reason'      => 'nullable|string|max:500',
    ];

    // إذا كان الطلب قادم من لوحة تحكم الإدارة، نحتاج employee_id
    if (!$request->expectsJson()) {
        $rules['employee_id'] = 'required|exists:employees,id';
    }

    $request->validate($rules);

    try {
        // 2. تحديد المعرف الخاص بالموظف
        // إذا كان الطلب من الموبايل (API)، نجلب الموظف من التوكن
        // إذا كان من الويب، نأخذه من المدخلات
        $employeeId = $request->expectsJson() 
                      ? auth()->user()->employee->id 
                      : $request->employee_id;

        if (!$employeeId) {
            return response()->json(['message' => 'بيانات الموظف غير مكتملة'], 400);
        }

        // 3. إنشاء السلفة
        $loan = Loan::create([
            'employee_id'      => $employeeId,
            'amount'           => $request->amount,
            'installment'      => $request->installment,
            'remaining_amount' => $request->amount,
            'reason'           => $request->reason,
            'start_date'       => now()->addMonth()->startOfMonth(),
            'status'           => 'pending',
        ]);

        // 4. الرد بناءً على نوع المنصة
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال طلب السلفة بنجاح ✅',
                'data' => $loan
            ], 201);
        }

        return redirect()->back()->with('success', 'تم تسجيل السلفة بنجاح ✅');

    } catch (\Exception $e) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'حدث خطأ في السيرفر: ' . $e->getMessage()], 500);
        }
        return redirect()->back()->with('error', 'حدث خطأ أثناء الحفظ، يرجى المحاولة لاحقاً.');
    }
}

    /**
     * الموافقة والترحيل المالي المباشر
     */
    public function approve(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        // لم نعد بحاجة للبحث عن employee عن طريق user_id لأن الربط أصبح مباشراً
        try {
            DB::transaction(function () use ($loan, $request) {
                
                // 1. تحديث حالة السلفة لنشطة
                $loan->update([
                    'status'      => 'active',
                    'admin_reply' => $request->admin_reply,
                    'start_date'  => now(), 
                ]);

                // 2. الترحيل للحاوية المالية باستخدام نفس الـ employee_id الموجود في السلفة
                FinancialTransaction::create([
                    'employee_id'      => $loan->employee_id,
                    'type'             => 'advance', 
                    'amount'           => $loan->amount,
                    'description'      => "سلفة معتمدة رقم: " . $loan->id . " - " . ($loan->reason ?? 'بدون وصف'),
                    'transaction_date' => now(),
                ]);
            });

            return redirect()->back()->with('success', 'تمت الموافقة والترحيل المالي للسجل الموحد بنجاح ✅');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطأ أثناء الترحيل المالي: ' . $e->getMessage());
        }
    }

    /**
     * رفض طلب السلفة
     */
    public function reject(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        
        $loan->update([
            'status'      => 'rejected',
            'admin_reply' => $request->admin_reply,
        ]);

        return redirect()->back()->with('info', 'تم رفض طلب السلفة بنجاح.');
    }

    /**
     * تحديث بيانات السلفة
     */
    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);
        
        $request->validate([
            'amount'      => 'required|numeric|min:1',
            'installment' => 'required|numeric|min:1',
            'start_date'  => 'required|date',
        ]);

        $loan->update([
            'amount'           => $request->amount,
            'installment'      => $request->installment,
            'start_date'       => $request->start_date,
            'remaining_amount' => ($loan->status == 'paid') ? 0 : $request->amount,
        ]);

        return redirect()->route('admin.loans.index')->with('success', 'تم تحديث بيانات السلفة بنجاح');
    }

    /**
     * حذف السلفة نهائياً
     */
    public function destroy(string $id)
    {
        $loan = Loan::findOrFail($id);
        
        // منع الحذف إذا كانت السلفة نشطة ولها سجلات مالية (اختياري لسلامة المحاسبة)
        if ($loan->status == 'active') {
            return redirect()->back()->with('error', 'لا يمكن حذف سلفة نشطة، يرجى رفضها أو تسويتها أولاً.');
        }

        $loan->delete();
        return redirect()->route('admin.loans.index')->with('success', 'تم حذف السلفة بنجاح');
    }
    // دالة جديدة لجلب سلف الموظف فقط (للتطبيق)
public function myLoans()
{
    $employee = auth()->user()->employee;
    $loans = Loan::where('employee_id', $employee->id)->latest()->get();
    
    // إضافة حقول مترجمة للسهولة في التطبيق
    $loans->transform(function($loan) {
        $loan->status_ar = match($loan->status) {
            'pending' => 'انتظار',
            'active'  => 'نشطة',
            'paid'    => 'مسددة',
            'rejected'=> 'مرفوض',
            default   => $loan->status
        };
        return $loan;
    });

    return response()->json($loans);
}
}