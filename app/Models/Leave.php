<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'reason',
        'attachment',
        'status',
        'admin_reply'
    ];

    // علاقة الإجازة بالموظف (كل إجازة تنتمي لموظف واحد)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}