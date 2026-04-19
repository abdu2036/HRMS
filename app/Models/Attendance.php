<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Attendance extends Model
{
  protected $fillable = [
    'employee_id', 'date', 'signin_time', 'signout_time', 
    'status', 'late_minutes', 'overtime_minutes', 'note',
    'lat', 'lng', 'distance_from_branch', 'early_out_minutes' // الحقول الجديدة
];

    // ربط الحضور بالموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}