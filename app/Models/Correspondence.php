<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correspondence extends Model
{
    use HasFactory;

    // الحقول المسموح بحفظها (Mass Assignment)
    protected $fillable = [
        'type',
        'direction',
        'reference_number',
        'subject',
        'content',
        'sender_id',
        'receiver_id',
        'receiver_department_id',
        'status',
        'read_at',
        'attachment'
    ];

    // تحويل الحقول الزمنية إلى كائنات Carbon تلقائياً
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * علاقة المراسلة بالمرسل (الموظف)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * علاقة المراسلة بالمستقبل (موظف محدد - إن وجد)
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * علاقة المراسلة بالقسم المستلم (مثلاً الحسابات)
     * ملاحظة: تأكد من اسم الموديل الخاص بالأقسام لديك (غالباً Department)
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'receiver_department_id');
    }

    /**
     * Helper Function: للتحقق هل الرسالة رسمية أم لا
     */
    public function isOfficial()
    {
        return $this->type === 'official';
    }
}