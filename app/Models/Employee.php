<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Reward;
use App\Models\Salary;



class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
       'user_id', 'employee_code', 'fingerprint_code','branch_id', 'full_name', 'gender', 
        'date_of_birth', 'marital_status', 'qualification', 'email', 
        'phone', 'address', 'national_id', 'id_expiry_date', 
        'department_id', 'job_title_id', 'shift_id', 'manager_id', 
        'basic_salary', 'iban', 'hire_date', 'leaving_date', 
        'employment_type', 'status', 'profile_photo', 'id_proof', 'total_leave_balance','notes',
        'health_certificate_expiry', 'health_certificate_file'
    ];

    // علاقة الموظف بالقسم
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // علاقة الموظف بالمسمى الوظيفي
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    // علاقة الموظف بالشفت (الوردية)
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // علاقة الموظف بمديره المباشر
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    // علاقة الموظف بالموظفين الذين يديرهم (المرؤوسين)
    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }
    // لإتاحة جلب سجلات حضور الموظف
public function attendances()
{
    return $this->hasMany(Attendance::class);
}
// ... داخل كلاس Employee

/**
 * علاقة الموظف بالفرع
 */
public function branch()
{
    // افترضنا أن الحقل في جدول الموظفين هو branch_id
    return $this->belongsTo(Branch::class, 'branch_id');
}

/**
 * علاقة الموظف بالوردية (Shift)
 */

/**
 * علاقة الموظف بالمستخدم (User)
 */
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
public function rewards()
{
    return $this->hasMany(Reward::class); // تأكد من اسم موديل المكافآت لديك
}

public function penalties()
{
    return $this->hasMany(Penalty::class); // تأكد من اسم موديل العقوبات لديك
}
public function leaves()
{
    return $this->hasMany(Leave::class);
}

// دالة ذكية لحساب الرصيد المتبقي (المرحلة 4)
public function getRemainingBalanceAttribute()
{
    $consumed = $this->leaves()
        ->where('leave_type', 'annual')
        ->where('status', 'approved')
        ->sum('days_count');
        
    return $this->total_leave_balance - $consumed;
}
public function loans()
{
    // افترضنا أن اسم موديل السلف هو Loan
    return $this->hasMany(Loan::class); 
}
public function custodies()
{
    return $this->hasMany(Custody::class);
}
public function financialTransactions()
{
    return $this->hasMany(FinancialTransaction::class);
}
public function payrolls()
{
    return $this->hasMany(Payroll::class);
}
public function salary() {
    return $this->hasOne(Salary::class);
}
public function payrollReports()
{
    return $this->hasMany(PayrollReport::class, 'employee_id');
}


public function ticketTransactions()
{
    // هذه الدالة تربط الموظف بجدول عمليات التذاكر
    return $this->hasMany(TicketTransaction::class, 'employee_id');
}

// أضف أيضاً هذه العلاقة إذا كنت تستخدمها في لوحة التحكم (الرصيد المتاح)
public function currentMonthAllowance()
{
    return $this->hasOne(TicketAllowance::class, 'employee_id')
                ->where('month', now()->month)
                ->where('year', now()->year);
}
public function earlyExitPermissions()
{
    return $this->hasMany(EarlyExitPermission::class);
}
}