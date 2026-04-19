<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    // الحقول التي يسمح لارافيل بتعبئتها (Fillable)
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
        'admin_notes',
        'payment_method',
    ];

    /**
     * علاقة الراتب بالموظف
     * كل راتب ينتمي لموظف واحد
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}