<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'address', 
        'phone', 
        'email', 
        'company_profile_id',
        'latitude', 
        'longitude', 
        'radius_meters'
    ];

    // علاقة: الفرع ينتمي لشركة واحدة
    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_profile_id');
    }

    // علاقة: الفرع يحتوي على العديد من الموظفين
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}