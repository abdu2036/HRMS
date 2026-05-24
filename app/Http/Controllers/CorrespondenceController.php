<?php

namespace App\Http\Controllers;

use App\Models\Correspondence;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // تأكد من استدعاء هذا الكلاس

class CorrespondenceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userDepartmentId = optional($user->employee)->department_id;

        $correspondences = Correspondence::with(['sender', 'department'])
            ->where(function($query) use ($user, $userDepartmentId) {
                if ($userDepartmentId) {
                    $query->where('receiver_department_id', $userDepartmentId);
                }
                $query->orWhere('receiver_id', $user->id)
                      ->orWhere('sender_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        return view('admin.correspondence.index', compact('correspondences'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.correspondence.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // 1. التحقق من البيانات (أضفنا المرفق هنا)
        $request->validate([
            'type' => 'required|in:official,internal',
            'subject' => 'required|string|max:255',
            'content' => 'required',
            'receiver_department_id' => 'required_without:receiver_id',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048', // تحديد الأنواع والحجم
        ]);

        $reference_number = null;
        if ($request->type === 'official') {
            $reference_number = $this->generateReferenceNumber();
        }

        // 2. منطق رفع الملف
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // حفظ الملف في مجلد attachments داخل القرص العام (public)
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        // 3. إنشاء المراسلة
        Correspondence::create([
            'type' => $request->get('type'),
            'direction' => 'outgoing',
            'subject' => $request->get('subject'),
            'content' => $request->get('content'),
            'sender_id' => Auth::id(),
            'receiver_department_id' => $request->get('receiver_department_id'),
            'receiver_id' => $request->get('receiver_id'),
            'reference_number' => $reference_number,
            'attachment' => $attachmentPath, // حفظ المسار في قاعدة البيانات
            'status' => 'sent',
        ]);

        return redirect()->route('correspondence.index')->with('success', 'تم إرسال المراسلة بنجاح ✅');
    }

    private function generateReferenceNumber()
    {
        $year = date('Y');
        $lastRef = Correspondence::where('type', 'official')
                    ->whereYear('created_at', $year)
                    ->count();
        
        $nextNumber = str_pad($lastRef + 1, 4, '0', STR_PAD_LEFT);
        return "DM-{$year}-{$nextNumber}";
    }

    public function show($id)
    {
        $correspondence = Correspondence::with(['sender', 'department'])->findOrFail($id);

        if (Auth::id() != $correspondence->sender_id && is_null($correspondence->read_at)) {
            $correspondence->update([
                'read_at' => now(),
                'status' => 'read'
            ]);
        }

        return view('admin.correspondence.show', compact('correspondence'));
    }

    public function print($id)
    {
        $correspondence = Correspondence::with(['sender', 'department'])->findOrFail($id);
        
        if ($correspondence->type !== 'official') {
            return redirect()->back()->with('error', 'هذه المراسلة ليست رسمية للطباعة.');
        }

        return view('admin.correspondence.print', compact('correspondence'));
    }

    // إضافة هذا الكونستركتور لمنع وصول الموظفين إلى المراسلات
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        // إذا كان المستخدم الحالي لديه دور موظف، اطرده فوراً خارج المراسلات
        if (auth()->user()->hasRole('employee')) {
            abort(403);
        }
        return $next($request);
    });
}
}