<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Employee;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // إضافة with('manager') لتحميل بيانات المدير فوراً
    $departments = Department::with('manager')
        ->orderByRaw('parent_id IS NOT NULL')
        ->orderBy('order', 'asc')
        ->get();

    // التأكد من جلب الموظفين النشطين (القيمة 1 كما في قاعدة بياناتك)
    $employees = Employee::where('status', 1)->orderBy('full_name', 'asc')->get();

    return view('departments.index', compact('departments', 'employees'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    // جلب الأقسام لتكون أقساماً أب (Parents)
    $parents = Department::whereNull('parent_id')->get();
    
    // جلب الموظفين ليتم اختيار أحدهم كمدير للقسم
    $employees = Employee::whereIn('status', [1, 'active'])
    ->orderBy('full_name', 'asc')
    ->get();
    return view('departments.create', compact('parents', 'employees'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التأكد من صحة البيانات (Validation)
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|unique:departments,code', // الكود يجب أن يكون فريداً
            'parent_id' => 'nullable|exists:departments,id',         // يجب أن يكون الأب موجوداً فعلاً
            'order'       => 'nullable|integer',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',                     // يجب أن يكون الحالة إما 1 أو 0

        ]);

        // بعد التحقق، نقوم بإنشاء القسم
        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'تم إضافة القسم بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit($id)
{
    $department = Department::findOrFail($id);
    $parents = Department::where('id', '!=', $id)->get();

    // التعديل هنا لقبول كلا النوعين من الحالات
    $employees = Employee::whereIn('status', [1, 'active'])
        ->orderBy('full_name', 'asc')
        ->get();

    return view('departments.edit', compact('department', 'parents', 'employees'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|unique:departments,code,' . $department->id,
        'manager_id' => 'nullable|exists:employees,id', // التأكد من صحة معرف المدير
        'status' => 'required|boolean',
    ]);

    // تحديث البيانات بما فيها manager_id
    $department->update($request->all());

    return redirect()->route('departments.index')->with('success', 'تم تحديث بيانات القسم بنجاح');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'تم حذف القسم بنجاح');
    }
}
