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
    Schema::create('payroll_reports', function (Blueprint $table) {
        $table->id(); // المعرف الفريد للإيصال (BigInt)
        
        // ربط السجل بالموظف - تأكد أن اسم الجدول هو employees
        $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
        
        $table->integer('month'); // الشهر (1-12)
        $table->integer('year');  // السنة (مثلاً 2026)
        
        // الحقول المالية بدقة Decimal 10,2 لضمان صحة الحسابات
        $table->decimal('basic_salary', 10, 2);    // الراتب الأساسي
        $table->decimal('total_bonuses', 10, 2)->default(0);   // إجمالي المكافآت
        $table->decimal('total_deductions', 10, 2)->default(0); // إجمالي الخصومات
        $table->decimal('loan_installment', 10, 2)->default(0); // قسط السلفة
        $table->decimal('held_assets', 10, 2)->default(0);     // العهد المخصومة
        $table->decimal('net_salary', 10, 2);      // الصافي النهائي المستلم
        
        $table->date('payment_date')->nullable(); // التاريخ الفعلي للصرف
        $table->string('status')->default('paid'); // حالة الراتب (paid, pending)
        
        $table->timestamps(); // created_at & updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_payroll_report');
    }
};
