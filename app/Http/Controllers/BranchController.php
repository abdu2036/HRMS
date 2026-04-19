<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\CompanyProfile;


class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  // 1. عرض قائمة الفروع
    public function index()
    {
        $branches = Branch::with('company')->get();
        return view('settings.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
   // 2. عرض نموذج إضافة فرع جديد
    public function create()
    {
        // نحتاج لجلب الشركة لنربط الفرع بها
        $company = CompanyProfile::first(); 
        return view('settings.branches.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     */
  // 3. حفظ الفرع الجديد في قاعدة البيانات
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_profile_id' => 'required|exists:company_profiles,id',
        ]);

        Branch::create($request->all());

        return redirect()->route('branches.index')
                         ->with('success', 'تم إضافة الفرع بنجاح! 🎉');
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
// 1. عرض صفحة التعديل مع بيانات الفرع المختار
public function edit($id)
{
    $branch = Branch::findOrFail($id);
    // نحتاج أيضاً لجلب الشركة للتأكد من الربط الصحيح
    $company = CompanyProfile::first(); 
    
    return view('settings.branches.edit', compact('branch', 'company'));
}

// 2. تحديث البيانات في قاعدة البيانات
public function update(Request $request, Branch $branch)
{
    $data = $request->validate([
        'name' => 'required|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'radius_meters' => 'nullable|integer',
        // باقي حقولك القديمة...
    ]);

    $branch->update($data);

    return redirect()->route('branches.index')->with('success', 'تم تحديث بيانات الفرع والموقع الجغرافي.');
}
    /**
     * Remove the specified resource from storage.
     */
   // حذف الفرع من قاعدة البيانات
public function destroy($id)
{
    // البحث عن الفرع
    $branch = Branch::findOrFail($id);

    // تنفيذ عملية الحذف
    $branch->delete();

    // العودة لصفحة القائمة مع رسالة تأكيد
    return redirect()->route('branches.index')
                     ->with('success', 'تم حذف الفرع بنجاح! 🗑️');
}
}
