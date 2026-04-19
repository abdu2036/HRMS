<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // تأكد من استيراد هذه

class Reward extends Model {
    
    protected $fillable = ['employee_id', 'date', 'type', 'days_count', 'description', 'amount'];

    // --- أضف هذه الدالة هنا ---
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}