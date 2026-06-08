<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Shift;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Salary;


class EmployeeController extends Controller
{
    public function index(Request $request) // أضفنا Request هنا لاستلام بيانات البحث
{
    // ابدأ بإنشاء استعلام مع العلاقات المطلوبة لضمان السرعة
    $query = Employee::with(['branch', 'department', 'jobTitle', 'shift', 'salary']);

    // التحقق من وجود كلمة بحث في الرابط (Request)
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;

        $query->where(function($q) use ($search) {
            $q->where('full_name', 'like', '%' . $search . '%')          // البحث بالاسم الكامل
              ->orWhere('employee_code', 'like', '%' . $search . '%')   // البحث بكود الموظف
              ->orWhere('phone', 'like', '%' . $search . '%')           // البحث برقم الهاتف
              ->orWhere('national_id', 'like', '%' . $search . '%');    // البحث بالرقم الوطني
        });
    }
    $query->orderBy('id', 'asc');

    // ترتيب الموظفين الأحدث أولاً مع استخدام الترقيم (Pagination) لضمان أداء سريع للجدول
    $employees = $query->latest()->paginate(10);

    // نمرر النتائج للـ view مع الاحتفاظ بكلمة البحث في الترقيم
    return view('admin.employees.index', compact('employees'))
           ->with('i', (request()->input('page', 1) - 1) * 10);
}

    public function create()
    {
        $departments = Department::all();
        $jobTitles   = JobTitle::all();
        $shifts      = Shift::all();
        $branches    = Branch::all(); 

        $lastEmployee = Employee::latest('id')->first();
        $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
        $generatedCode = 'EMP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        
        return view('admin.employees.create', compact('departments', 'jobTitles', 'shifts', 'branches', 'generatedCode'));
    }

 public function store(Request $request)
{
    // 1. التحقق من البيانات (أضفنا حقول الشهادة الصحية والفرع)
    $data = $request->validate([
        'user_id'          => 'nullable|exists:users,id',
        'employee_code'    => 'required|unique:employees,employee_code',
        'fingerprint_code' => 'nullable|unique:employees,fingerprint_code',
        'full_name'        => 'required|string|max:255',
        'gender'           => 'required|in:male,female',
        'date_of_birth'    => 'required|date',
        'marital_status'   => 'required|in:single,married,widowed,divorced',
        'qualification'    => 'required|string|max:255',
        'email'            => 'nullable|email|unique:employees,email',
        'phone'            => 'required|string|max:20',
        'address'          => 'required|string|max:255',
        'national_id'      => 'required|string|unique:employees,national_id',
        'id_expiry_date'   => 'required|date',
        'branch_id'        => 'required|exists:branches,id',
        'department_id'    => 'required|exists:departments,id',
        'job_title_id'     => 'required|exists:job_titles,id',
        'shift_id'         => 'required|exists:shifts,id',
        'manager_id'       => 'nullable|exists:employees,id',
        'basic_salary'     => 'required|numeric|min:0',
        'iban'             => 'nullable|string|max:255',
        'total_leave_balance' => 'required|numeric|min:0',
        'hire_date'        => 'required|date',
        'employment_type'  => 'required|in:full_time,part_time,contract,intern',
        'status'           => 'required',
        'profile_photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'id_proof'         => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        'notes'            => 'nullable|string',
        'health_certificate_expiry' => 'nullable|date',
        'health_certificate_file'   => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        'monthly_tickets_limit' => 'required|integer|min:0',
    ]);

    // 2. معالجة الصور والملفات وتخزينها في المسارات الصحيحة
    if ($request->hasFile('profile_photo')) {
        $data['profile_photo'] = $request->file('profile_photo')->store('employees/photos', 'public');
    }

    if ($request->hasFile('id_proof')) {
        $data['id_proof'] = $request->file('id_proof')->store('employees/documents', 'public');
    }

    // رفع الشهادة الصحية في مجلد خاص بها
    if ($request->hasFile('health_certificate_file')) {
        $data['health_certificate_file'] = $request->file('health_certificate_file')->store('employees/health_certificates', 'public');
    }

    // 3. تحويل حالة الموظف الرقمية
    $data['status'] = ($request->status == 'active') ? 1 : 0;

    // 4. الحفظ باستخدام Transaction لضمان ترابط البيانات
    return DB::transaction(function () use ($data, $request) {
        
        // إنشاء سجل الموظف
        $employee = Employee::create($data); 

        // إنشاء سجل الراتب المرتبط بالموظف الجديد
        Salary::create([
            'employee_id'    => $employee->id, 
            'basic_salary'   => $request->basic_salary,
            'allowances'     => 0, // يمكنك تعديلها لاحقاً لإضافة بدلات من الفورم
            'effective_date' => $request->hire_date,
        ]);
        // ج. [الجديد] إنشاء رصيد التذاكر للشهر الحالي
        \App\Models\TicketAllowance::create([
            'employee_id'     => $employee->id,
            'monthly_limit'   => $request->monthly_tickets_limit,
            'current_balance' => $request->monthly_tickets_limit, // يبدأ بالرصيد كاملاً
            'year'            => now()->year,
            'month'           => now()->month,
        ]);
        return redirect()->route('employees.index')->with('success', 'تم إضافة الموظف "'. $employee->full_name .'" وإنشاء سجل الراتب بنجاح');
    });
}
    public function show(Employee $employee)
    {
        // أضفنا 'branch' هنا أيضاً لصفحة العرض
        $employee->load(['branch', 'department', 'jobTitle', 'shift']);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $jobTitles = JobTitle::all();
        $shifts = Shift::all();
        $branches = Branch::all(); 

        return view('admin.employees.edit', compact('employee', 'departments', 'jobTitles', 'shifts', 'branches'));
    }

public function update(Request $request, Employee $employee)
{
    // 1. التحقق من صحة البيانات
    $validatedData = $request->validate([
        'user_id'                   => 'nullable|exists:users,id',
        'employee_code'             => 'required|unique:employees,employee_code,' . $employee->id,
        'fingerprint_code'          => 'nullable|unique:employees,fingerprint_code,' . $employee->id,
        'full_name'                 => 'required|string|max:255',
        'gender'                    => 'required|in:male,female',
        'date_of_birth'             => 'required|date',
        'marital_status'            => 'required|in:single,married,widowed,divorced',
        'qualification'             => 'required|string|max:255',
        'email'                     => 'nullable|email|unique:employees,email,' . $employee->id,
        'phone'                     => 'required|string|max:20',
        'address'                   => 'required|string|max:255',
        'national_id'               => 'required|string|unique:employees,national_id,' . $employee->id,
        'id_expiry_date'            => 'required|date',
        'branch_id'                 => 'required|exists:branches,id',
        'department_id'             => 'required|exists:departments,id',
        'job_title_id'              => 'required|exists:job_titles,id',
        'shift_id'                  => 'required|exists:shifts,id',
        'manager_id'                => 'nullable|exists:employees,id',
        'basic_salary'              => 'required|numeric|min:0',
        'iban'                      => 'nullable|string|max:255',
        'total_leave_balance'       => 'required|numeric|min:0',
        'hire_date'                 => 'required|date',
        'employment_type'           => 'required|in:full_time,part_time,contract,intern',
        'status'                    => 'required',
        'profile_photo'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'id_proof'                  => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        'notes'                     => 'nullable|string',
        'health_certificate_expiry' => 'nullable|date',
        'health_certificate_file'   => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        'monthly_tickets_limit'     => 'required|integer|min:0',
    ]);

    // 2. تجهيز البيانات للتحديث
    $updateData = $request->all();

    // 3. معالجة الصورة الشخصية
    if ($request->hasFile('profile_photo')) {
        if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) { 
            Storage::disk('public')->delete($employee->profile_photo); 
        }
        $updateData['profile_photo'] = $request->file('profile_photo')->store('employees/photos', 'public');
    }

    // 4. معالجة إثبات الهوية
    if ($request->hasFile('id_proof')) {
        if ($employee->id_proof && Storage::disk('public')->exists($employee->id_proof)) { 
            Storage::disk('public')->delete($employee->id_proof); 
        }
        $updateData['id_proof'] = $request->file('id_proof')->store('employees/documents', 'public');
    }

    // 5. معالجة الشهادة الصحية
    if ($request->hasFile('health_certificate_file')) {
        if ($employee->health_certificate_file && Storage::disk('public')->exists($employee->health_certificate_file)) { 
            Storage::disk('public')->delete($employee->health_certificate_file); 
        }
        $updateData['health_certificate_file'] = $request->file('health_certificate_file')->store('employees/health_certs', 'public');
    }

    // تنفيذ التحديث داخل عملية واحدة (Transaction) لضمان سلامة البيانات
    return DB::transaction(function () use ($employee, $updateData, $request) {
        
        // أ. تحديث بيانات الموظف الأساسية
        $employee->update($updateData);

        // ب. تحديث أو إنشاء سجل الراتب
        $currentSalary = \App\Models\Salary::where('employee_id', $employee->id)->first();
        \App\Models\Salary::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'basic_salary'   => $request->basic_salary,
                'effective_date' => $employee->hire_date ?? now(),
                'allowances'     => $currentSalary->allowances ?? 0
            ]
        );

        // ج. تحديث أو إنشاء رصيد التذاكر للشهر الحالي (الربط مع Dashboard)
        // سيقوم هذا الجزء بإنشاء سجل في جدول ticket_allowances لكي يظهر الرقم في شاشة الموظف
        \App\Models\TicketAllowance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'year'        => now()->year,
                'month'       => now()->month,
            ],
            [
                'initial_balance' => $request->monthly_tickets_limit,
                'current_balance' => $request->monthly_tickets_limit, // يتم ضبط الرصيد المتاح بالقيمة المدخلة
                'used_balance'    => 0,
            ]
        );

        return redirect()->route('employees.index')->with('success', 'تم تحديث بيانات الموظف بنجاح.');
    });
}

    public function destroy(Employee $employee)
    {
        if ($employee->profile_photo) { Storage::disk('public')->delete($employee->profile_photo); }
        if ($employee->id_proof) { Storage::disk('public')->delete($employee->id_proof); }

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف وكافة ملفاته بنجاح');
    }
public function createAccount($id)
{
    // 1. جلب الموظف مع التأكد من وجوده
    $emp = Employee::findOrFail($id);

    // 2. التحقق من وجود بريد إلكتروني (أساسي لإنشاء حساب)
    if (empty($emp->email)) {
        return redirect()->back()->with('error', 'عذراً، الموظف ليس لديه بريد إلكتروني مسجل!');
    }

    // 3. التحقق من التكرار بشكل منفصل لتقديم رسالة دقيقة
    if ($emp->user_id) {
        return redirect()->back()->with('error', 'هذا الموظف مرتبط بالفعل بحساب مستخدم.');
    }

    $userExists = User::where('email', $emp->email)->first();
    if ($userExists) {
        return redirect()->back()->with('error', 'البريد الإلكتروني (' . $emp->email . ') مستخدم بالفعل لحساب آخر.');
    }

    // 4. تحديد الاسم بدقة (جربنا full_name ثم name ثم fallback)
    $nameToUse = $emp->full_name ?? $emp->name ?? 'موظف جديد';

    try {
        // استخدام DB Transaction لضمان أنه إذا فشل تحديث الموظف يتم حذف المستخدم المنشأ
        return DB::transaction(function () use ($emp, $nameToUse) {
            
            // 5. إنشاء المستخدم الجديد
            $user = new User();
            $user->name = $nameToUse;
            $user->email = $emp->email;
            $user->password = Hash::make('12345678');
            
            // إضافة employee_id في جدول Users إذا كان الحقل موجوداً لزيادة الربط
            if (Schema::hasColumn('users', 'employee_id')) {
                $user->employee_id = $emp->id;
            }
            
            $user->save();

            // 6. التحديث في جدول employees (الربط العكسي)
            // تأكد أن user_id موجود في الـ $fillable داخل موديل Employee
            $emp->user_id = $user->id;
            $emp->save();

            // 7. إعطاء الصلاحية (Spatie)
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('employee');
            }

            return redirect()->route('employees.index')
                ->with('success', 'تم إنشاء الحساب بنجاح للموظف: ' . $nameToUse . ' | كلمة المرور: 12345678');
        });

    } catch (\Exception $e) {
        // تسجيل الخطأ في ملف الـ log لمراجعته
        Log::error("خطأ في إنشاء حساب للموظف $id: " . $e->getMessage());
        
        return redirect()->back()->with('error', 'حدث خطأ فني: ' . $e->getMessage());
    }
}
public function idCard($id) {
    // جلب الموظف مع علاقات الفرع والمسمى الوظيفي
    $employee = Employee::with(['department', 'jobTitle', 'branch'])->findOrFail($id);
    return view('admin.employees.id_card_print', compact('employee'));
}
}