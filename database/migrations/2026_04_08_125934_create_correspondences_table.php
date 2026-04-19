<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('correspondences', function (Blueprint $table) {
        $table->id();
        
        // نوع المراسلة واتجاهها
        $table->enum('type', ['official', 'internal'])->default('internal'); // رسمي أو داخلي عادي
        $table->enum('direction', ['incoming', 'outgoing'])->default('outgoing'); // وارد أو صادر
        
        // البيانات الأساسية
        $table->string('reference_number')->nullable()->unique(); // الرقم الإشاري (للرسمي فقط)
        $table->string('subject'); // موضوع المراسلة
        $table->longText('content'); // نص المراسلة
        
        // العلاقات (المرسل والمستقبل)
        $table->foreignId('sender_id')->constrained('users'); // الموظف الذي أنشأ الرسالة
        $table->integer('receiver_id')->nullable(); // الموظف المستلم (اختياري)
        $table->integer('receiver_department_id')->nullable(); // القسم المستلم (مثل الحسابات)
        
        // الحالة والأرشفة
        $table->enum('status', ['draft', 'sent', 'read', 'archived'])->default('sent');
        $table->timestamp('read_at')->nullable(); // متى تمت القراءة (لشعار الاستلام)
        
        // حقل إضافي للمرفقات إذا أرادوا رفع ملف PDF خارجي
        $table->string('attachment')->nullable(); 

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correspondences');
    }
};
