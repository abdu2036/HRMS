<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تنظيف التخزين المؤقت للصلاحيات (مهم جداً عند التعديل)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. تعريف قائمة الصلاحيات الشاملة للنظام
        $permissions = [
            // إدارة الهيكل التنظيمي
            'manage companies',
            'manage branches',
            'manage departments',
            
            // إدارة الموارد البشرية
            'manage employees',
            'view employees',
            'manage shifts',
            'manage attendance',
            
            // الإعدادات والصلاحيات
            'manage roles',
            'manage users',
            'manage settings',
            
            // التقارير
            'view reports',
        ];

        // 3. إنشاء الصلاحيات في قاعدة البيانات (تستخدم firstOrCreate لتجنب التكرار)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 4. إنشاء أو تحديث دور "المدير العام" (super-admin)
        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        
        // إعطاء المدير العام كافة الصلاحيات الموجودة حالياً
        $adminRole->syncPermissions(Permission::all());

        // 5. منح الدور للمستخدم المسؤول
        $user = User::where('email', 'abdsalaam2011@gmail.com')->first();
        if ($user) {
            // نستخدم syncRoles لضمان عدم تكرار الدور
            $user->syncRoles([$adminRole->name]);
        }
    }
}