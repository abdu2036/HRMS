<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Employee;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    // جلب المستخدمين مع أدوارهم 👥
    $users = User::with('roles')->get();
    
    return view('users.index', compact('users'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
public function edit(User $user)
{
    $roles = Role::all();
    $userRole = $user->roles->pluck('name')->toArray();
    
    // جلب كافة الموظفين لربط أحدهم بالحساب
    $employees = Employee::all(); 

    return view('users.edit', compact('user', 'roles', 'userRole', 'employees'));
}
    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, User $user)
{
    // 1. التحقق من البيانات (أضفنا التحقق من الموظف)
    $request->validate([
        'role' => 'required|exists:roles,name',
        'employee_id' => 'nullable|exists:employees,id', // التأكد أن الموظف موجود
    ]);

    // 2. تحديث الدور
    $user->syncRoles($request->role);

    // 3. الربط السحري: تحديث حقل employee_id في جدول users
    $user->update([
        'employee_id' => $request->employee_id
    ]);

    return redirect()->route('users.index')->with('success', 'تم تحديث بيانات وصلاحيات المستخدم بنجاح');
}

    /**
     * Remove the specified resource from storage.
     */public function destroy(User $user)
{
    $user->delete();
    return back()->with('success', 'تم حذف المستخدم بنجاح');
}
public function employee() {
    return $this->belongsTo(Employee::class, 'employee_id');
}
}