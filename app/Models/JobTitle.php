<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'description',
        'min_salary',
        'max_salary',
        'status'
    ];
    // ⬇️ أضف هذه الدالة هنا هي "المحرك" الذي يربط الوظيفة بالقسم
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
