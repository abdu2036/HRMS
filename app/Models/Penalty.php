<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    // السماح بتعبئة الحقول (Mass Assignment)
   
protected $fillable = [
    'employee_id',
    'amount',
    'description',
    'date',        // تأكد من إضافة هذا
    'days_count',  // تأكد من إضافة هذا
    'type',        // تأكد من إضافة هذا
    'status',
];
    // علاقة الجزاء بالموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}