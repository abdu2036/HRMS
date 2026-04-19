<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollReport extends Model
{
    use HasFactory;

    // تحديد اسم الجدول يدوياً لضمان الربط الصحيح
    protected $table = 'payroll_reports';

    // الحقول المسموح بتعبئتها (Mass Assignment)
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'total_bonuses',
        'total_deductions',
        'loan_installment',
        'held_assets',
        'net_salary',
        'payment_date',
        'status',
        'payment_method',
    ];

    /**
     * علاقة الموظف: كل سجل راتب ينتمي لموظف واحد
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}