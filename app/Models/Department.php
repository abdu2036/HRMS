<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'manager_id',
        'order',
        'status',
        
    ];
    // علاقة القسم بالأب (الإدارة الأعلى منه)
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    // علاقة القسم بالأقسام الفرعية (الإدارات الأقل منه)
   // علاقة الأقسام التابعة (الأبناء)
public function children()
{
    return $this->hasMany(Department::class, 'parent_id')->orderBy('order', 'asc');
}
public function manager()
{
    // نفترض أن اسم العمود في جدول الأقسام هو manager_id
    return $this->belongsTo(Employee::class, 'manager_id');
}
}