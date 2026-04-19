<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
{
    // جلب جميع الأدوار من قاعدة البيانات
    $roles = Role::all();

    // إرسال الأدوار إلى صفحة العرض (View) التي سنقوم بإنشائها لاحقاً
    return view('roles.index', compact('roles'));
}
// هذه الدالة وظيفتها فقط عرض الصفحة (الفورم)
public function create()
{
    // سنجلب الصلاحيات أيضاً لنعرضها للمستخدم ليختار منها عند إنشاء الدور
    $permissions = Permission::all();
    return view('roles.create', compact('permissions'));
}

// هذه الدالة وظيفتها استقبال البيانات وحفظها
public function store(Request $request)
{
    // التحقق من صحة البيانات (الاسم مطلوب وفريد)
    $request->validate([
        'name' => 'required|unique:roles,name',
        'permissions' => 'array' // التأكد من أن الصلاحيات المختارة مصفوفة
    ]);

    // إنشاء الدور
    $role = Role::create(['name' => $request->name]);

    // ربط الصلاحيات المختارة بالدور الجديد
    if ($request->has('permissions')) {
        $role->syncPermissions($request->permissions);
    }

    return redirect()->route('roles.index')->with('success', 'تم إنشاء الدور بنجاح');
}
public function destroy(Role $role)
{
    // حذف الدور من قاعدة البيانات
    $role->delete();

    // العودة مع رسالة نجاح زرقاء كما صممنا في الواجهة
    return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح ✅');
}
public function edit(Role $role)
{
    $permissions = Permission::all();
    // جلب أسماء الصلاحيات التي يمتلكها الدور حالياً
    $rolePermissions = $role->permissions->pluck('name')->toArray();
    
    return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
}
public function update(Request $request, Role $role)
{
    $request->validate([
        'name' => 'required|unique:roles,name,' . $role->id,
        'permissions' => 'array'
    ]);

    // تحديث اسم الدور
    $role->update(['name' => $request->name]);

    // مزامنة الصلاحيات كما اقترحت بنجاح!
    $role->syncPermissions($request->permissions);

    return redirect()->route('roles.index')->with('success', 'تم تحديث الدور وصلاحياته بنجاح 💙');
}
}
