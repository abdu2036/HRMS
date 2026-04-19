<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // 1. استيراد الـ trait


class User extends Authenticatable
{
    // 2. إضافة HasRoles هنا لتمكين خصائص الصلاحيات
    use HasApiTokens, HasFactory, Notifiable, HasRoles; 

    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
//public function employee()
//{
    // نحدد أن الربط يتم عبر حقل user_id الموجود في جدول الموظفين
    //return $this->hasOne(Employee::class, 'user_id', 'id');
//}

public function employee()
{
    // المستخدم "ينتمي" لموظف عبر حقل employee_id الموجود في جدول users
    return $this->belongsTo(Employee::class, 'employee_id');
}



}
