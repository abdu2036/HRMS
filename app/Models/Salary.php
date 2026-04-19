<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

protected $fillable = [
    'employee_id',// ربط الراتب بالموظف
    'basic_salary',// الراتب الأساسي
    'allowances',// إجمالي البدلات الثابتة
    'effective_date',// تاريخ بدء العمل بهذا الراتب
];

// علاقة الراتب بالموظف
public function employee()
{
    // تأكد أنها تشير لـ Employee::class
    return $this->belongsTo(Employee::class, 'employee_id'); 
}

}
