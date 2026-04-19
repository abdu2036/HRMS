<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // جلب أول سجل من جدول بيانات الشركة
        $company = CompanyProfile::first();

        // إرسال البيانات للملف
        return view('settings.company-profile.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.company-profile.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. التحقق من البيانات (Validation) 验证
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // الحد الأقصى 2MB
        ]);

        // 2. معالجة رفع الصورة (إذا وجدت) 🖼️
        $imagePath = null;
        if ($request->hasFile('company_logo')) {
            // حفظ الصورة في مجلد public/logos داخل الـ storage
            $imagePath = $request->file('company_logo')->store('logos', 'public');
        }

        // 3. الحفظ في قاعدة البيانات 💾
        CompanyProfile::create([
            'company_name'  => $request->company_name,
            'company_logo'  => $imagePath,
            'address'       => $request->address,
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email,
            'website'       => $request->website,
        ]);

        // 4. إعادة التوجيه لصفحة الاندكس مع رسالة نجاح ✨
        return redirect()->route('company-profile.index')->with('success', 'تم حفظ بيانات الشركة بنجاح!');
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
    public function edit($id)
    {
        // جلب بيانات الشركة باستخدام المعرف (ID)
        $company = CompanyProfile::findOrFail($id);

        // توجيه المستخدم لصفحة التعديل مع إرسال بيانات الشركة
        return view('settings.company-profile.edit', compact('company'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $company = CompanyProfile::findOrFail($id);

    // 1. التحقق من البيانات
    $request->validate([
        'company_name' => 'required|string|max:255',
        'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // 2. تحديث البيانات النصية
    $data = $request->all();

    // 3. معالجة الصورة الجديدة (فقط إذا تم رفع ملف جديد) 🖼️
    if ($request->hasFile('company_logo')) {
        // تخزين الصورة الجديدة
        $data['company_logo'] = $request->file('company_logo')->store('logos', 'public');
    } else {
        // إذا لم يرفع صورة، نحافظ على القيمة القديمة الموجودة في قاعدة البيانات
        $data['company_logo'] = $company->company_logo;
    }

    $company->update($data);

    return redirect()->route('company-profile.index')->with('success', 'تم تحديث بيانات الشركة بنجاح! ✨');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $company = CompanyProfile::findOrFail($id);

        // ملاحظة احترافية: إذا كان هناك شعار، يفضل حذفه من التخزين أيضاً
        if ($company->company_logo) {
            Storage::disk('public')->delete($company->company_logo);
        }

        $company->delete();

        return redirect()->route('company-profile.index')->with('success', 'تم حذف بيانات الشركة بنجاح! 🗑️');
    }
}
