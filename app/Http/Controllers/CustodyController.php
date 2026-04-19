<?php

namespace App\Http\Controllers;

use App\Models\Custody;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // لا تنسى استدعاء المكتبة في الأعلى
use \ArPHP\I18N\Arabic;
use App\Models\FinancialTransaction;
class CustodyController extends Controller
{
    // 1. عرض قائمة العهد
    public function index()
{
    $custodies = Custody::with('employee')->get();
    return view('admin.custodies.index', compact('custodies'));
}

    // 2. عرض نموذج إضافة عهدة


public function create()
{
    // جلب الموظفين (تأكد أن جدول employees يحتوي على بيانات فعلاً)
    $employees = Employee::all(); 

    // إذا كنت تريد الموظفين النشطين فقط (اختياري):
    // $employees = Employee::where('status', 'active')->get();

    return view('admin.custodies.create', compact('employees'));
}

    // 3. حفظ العهدة الجديدة
public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'type' => 'required|in:hardware,financial,both',
        'amount' => 'nullable|numeric',
        'hardware_details' => 'nullable|string',
        'name' => 'required|string',
    ]);

    Custody::create([
        'employee_id' => $request->employee_id,
        'name' => $request->name,
        'type' => $request->type,
        // إذا كان "both" أو "financial" نأخذ المبلغ، غير ذلك نضعه 0
        'amount' => ($request->type == 'hardware') ? 0 : ($request->amount ?? 0),
        'notes' => "تفاصيل: " . ($request->hardware_details ?? 'لا يوجد') . " | ملاحظات: " . ($request->notes ?? 'لا يوجد'),
        'status' => 'received',
    ]);

    return redirect()->route('custodies.index')->with('success', 'تم تسجيل العهدة بنجاح ✅');
}

    // 4. تحديث حالة العهدة (إرجاع أو تسجيل عجز)
public function updateStatus(Request $request, $id)
{
    $custody = Custody::findOrFail($id);
    
    // حفظ الحالة الجديدة سواء كانت مستلمة، مؤجلة، أو عجز
    $custody->status = $request->status;

    // إذا تم اختيار عجز، نقوم بحفظ المبلغ
    if ($request->status == 'shortage') {
        $custody->shortage_amount = $request->shortage_amount;
    } else {
        // إذا لم يكن عجز، نصفر حقل العجز لضمان نظافة البيانات
        $custody->shortage_amount = 0;
    }

    $custody->notes = $request->notes;
    $custody->save();

    return back()->with('success', 'تم تحديث حالة العهدة بنجاح');
}




// أضف هذا السطر في أعلى ملف الكنترولر مع بقية الـ Use


public function printReceipt($id)
{
    $custody = Custody::with('employee')->findOrFail($id);

    // إنشاء كائن المكتبة لمعالجة النصوص العربية
    $arabic = new Arabic();

    // دالة المعالجة للنصوص العربية (التي تمنع الحروف المقطعة في PDF)
    $p = function($text) use ($arabic) {
        if (!$text) return "";
        return $arabic->utf8Glyphs($text);
    };

    // جلب اسم الموظف لاستخدامه في الدمج
    $empName = $custody->employee->full_name;

    // 🟢 تحديد النصوص بناءً على الحالة (استلام أم إرجاع) مع دمج الاسم فوراً
    if ($custody->status == 'returned') {
        // نصوص إخلاء الطرف (المخالصة)
        $labels = [
            'title'   => $p("إيصال إخلاء طرف عهدة"),
            // قمنا بدمج الاسم داخل الجملة هنا قبل تمريرها للدالة $p لحل مشكلة الترتيب
            'declare' => $p("تشهد إدارة الشركة بأن الموظف السيد/ " . $empName),
            'ack'     => $p("قد قام بإرجاع العهدة الموضحة بياناتها أدناه إلى عهدة الشركة، وبذلك يعتبر ذمته بريئة منها نهائياً."),
            'th1'     => $p("اسم العهدة المرجعة"),
            'th2'     => $p("النوع"),
            'th3'     => $p("حالة الإرجاع / ملاحظات"),
            'f1'      => $p("توقيع المستلم (أمين المخزن)"),
            'f2'      => $p("اعتماد الإدارة"),
        ];
    } else {
        // نصوص الاستلام العادية
        $labels = [
            'title'   => $p("إيصال استلام عهدة موظف"),
            // قمنا بدمج الاسم داخل الجملة هنا أيضاً
            'declare' => $p("أقر أنا الموظف/ " . $empName),
            'ack'     => $p("بأنني قد استلمت العهدة الموضحة أدناه بصفة رسمية، وأتحمل المسؤولية الكاملة عنها."),
            'th1'     => $p("اسم العهدة"),
            'th2'     => $p("النوع"),
            'th3'     => $p("التفاصيل / ملاحظات"),
            'f1'      => $p("توقيع الموظف المستلم"),
            'f2'      => $p("اعتماد الموارد البشرية"),
        ];
    }

    // معالجة البيانات المتغيرة في الجدول
    $data = [
        'id'    => $custody->id,
        'name'  => $p($custody->name),
        'notes' => $p($custody->notes),
        'type'  => $p($custody->type == 'financial' ? 'مالية' : ($custody->type == 'hardware' ? 'جهاز' : 'جهاز + مالية')),
        'date'  => date('Y-m-d'),
    ];

    // إنشاء الـ PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.custodies.pdf', compact('data', 'labels'))
              ->setOptions([
                  'defaultFont' => 'sans-serif',
                  'isHtml5ParserEnabled' => true,
                  'isRemoteEnabled' => true,
              ]);

    // عرض الملف في المتصفح
    return $pdf->stream('receipt_'.$custody->id.'.pdf');
}
public function toggleStatus($id)
{
    $custody = Custody::findOrFail($id);

    // تبديل الحالة
    if ($custody->status == 'received') {
        $custody->status = 'deferred';
        $msg = "تم تأجيل العهدة بنجاح (لن تظهر في مرتب هذا الشهر)";
    } else {
        $custody->status = 'received';
        $msg = "تم إدراج العهدة في المرتب بنجاح";
    }

    $custody->save();
    return back()->with('success', $msg);
}

}