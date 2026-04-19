<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;

    // تحديد الحقول المسموح بتعبئتها
    protected $fillable = [
        'company_name',
        'address',
        'company_phone',
        'company_email',
        'website',
        'company_logo', // أضفنا هذا الحقل هنا
    ];
    public function branches()
{
    return $this->hasMany(Branch::class);
}
}