<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\User;

class EarlyExitPermission extends Model
{
    use HasFactory;

    // تحديد الأعمدة التي يسمح بحفظ البيانات فيها (Mass Assignment)
    protected $fillable = [
        'employee_id',
        'date',
        'allowed_exit_time',
        'reason',
        'created_by'
    ];

    // علاقة تربط الإذن بالموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // علاقة تربط الإذن بالمدير الذي أنشأه
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}