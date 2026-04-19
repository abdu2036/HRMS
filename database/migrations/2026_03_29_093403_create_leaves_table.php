<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('leaves', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
        
        // 1. أنواع الإجازات
        $table->enum('leave_type', ['annual', 'sick', 'unpaid', 'emergency']);
        
        $table->date('start_date');
        $table->date('end_date');
        $table->integer('days_count'); // سيتم حسابه برمجياً
        $table->text('reason')->nullable();
        
        // لإجازة المرضى (رفع التقرير الطبي)
        $table->string('attachment')->nullable(); 

        // 2. دورة حياة الطلب
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('admin_reply')->nullable(); // سبب الرفض مثلاً
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
