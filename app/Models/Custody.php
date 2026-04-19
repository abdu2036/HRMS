<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Custody extends Model
{
  protected $fillable = [
    'employee_id',
    'name',
    'type',
    'amount',
    'status',
    'shortage_amount', // أضف هذا
    'notes'           // وأضف هذا
];

    // علاقة العهدة بالموظف (كل عهدة تنتمي لموظف واحد)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    // داخل App\Models\Custody.php


// داخل موديل Custody.php أو Employee.php
public function getPostponedDetailsAttribute()
{
    if ($this->held_amount > 0) {
        // حذفنا subMonth() ليعرض شهر 4 إذا كان التاريخ في شهر 4
        return "مؤجلة من شهر " . $this->created_at->format('n');
    }
    return null;
}
}
