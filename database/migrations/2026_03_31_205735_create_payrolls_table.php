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
    Schema::create('payrolls', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
        
        // تفاصيل الوقت
        $table->integer('month'); // رقم الشهر (1-12)
        $table->integer('year');  // السنة (2026)
        
        // المبالغ المالية (نستخدم decimal لدقة الحسابات)
        $table->decimal('basic_salary', 10, 2);      // الراتب الأساسي وقت الصرف
        $table->decimal('total_bonuses', 10, 2)->default(0);    // إجمالي المكافآت (+)
        $table->decimal('total_deductions', 10, 2)->default(0); // إجمالي العقوبات (-)
        $table->decimal('loan_installment', 10, 2)->default(0); // قسط السلفة المخصوم (-)
        $table->decimal('held_assets', 10, 2)->default(0);      // العهد المحجوزة (-)
        
        $table->decimal('net_salary', 10, 2);        // الصافي النهائي المستلم
        
        $table->date('payment_date')->nullable();    // تاريخ الصرف الفعلي
        $table->enum('status', ['pending', 'paid'])->default('pending'); // حالة الراتب
        
        $table->text('admin_notes')->nullable();     // ملاحظات المدير
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
