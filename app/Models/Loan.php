<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
class Loan extends Model
{
    use HasFactory;
protected $fillable = [
    'employee_id', 'amount', 'installment', 'remaining_amount', 
    'start_date', 'status', 'reason', 'admin_reply'
];

// علاقة السلفة بالموظف

public function employee() {
    return $this->belongsTo(Employee::class, 'employee_id');
}
}
