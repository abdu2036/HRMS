<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\JobTitle;
use Illuminate\Http\Request;

class JobTitleController extends Controller
{
    public function index()
    {
        $jobTitles = JobTitle::with('department')->orderBy('id', 'desc')->get();
        // هذا المسار يطابق الصورة التي أرسلتها لمجلد views
        return view('job_titles.index', compact('jobTitles'));
    }

  public function create()
{
    // جلب جميع الأقسام لعرضها في القائمة المنسدلة
    $departments = Department::where('status', 1)->get();
    
    return view('job_titles.create', compact('departments'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'department_id' => 'required|exists:departments,id', // التأكد أن القسم موجود فعلياً
        'name'          => 'required|string|max:255',
        'description'   => 'nullable|string',
        'min_salary'    => 'nullable|numeric',
        'max_salary'    => 'nullable|numeric',
    ]);

    JobTitle::create($validated);

    return redirect()->route('job-titles.index')->with('success', 'تم إضافة المسمى الوظيفي وربطه بالقسم بنجاح');
}
// 1. دالة عرض صفحة التعديل
    public function edit(JobTitle $jobTitle)
    {
        // جلب جميع الأقسام ليتمكن المستخدم من تغيير قسم الوظيفة
        $departments = Department::where('status', 1)->get();
        
        return view('job_titles.edit', compact('jobTitle', 'departments'));
    }

    // 2. دالة تنفيذ التحديث في قاعدة البيانات
    public function update(Request $request, JobTitle $jobTitle)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'min_salary'    => 'nullable|numeric',
            'max_salary'    => 'nullable|numeric',
            'status'        => 'required|boolean',
        ]);

        $jobTitle->update($validated);

        return redirect()->route('job-titles.index')->with('success', 'تم تحديث المسمى الوظيفي بنجاح');
    }

    // 3. دالة الحذف
    public function destroy(JobTitle $jobTitle)
    {
        $jobTitle->delete();
        return redirect()->route('job-titles.index')->with('success', 'تم حذف المسمى الوظيفي بنجاح');
    }
}