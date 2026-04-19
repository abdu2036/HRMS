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
    Schema::create('financial_transactions', function (Blueprint $table) {
        $table->id();
        
        // ربط العملية بالموظف
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');

        // نوع العملية (للتفريق بين الداخل والخارج من الراتب)
        // bonus = مكافأة (+) 
        // penalty = جزاء إداري (-)
        // custody_deficit = عجز عهدة (-)
        $table->enum('type', ['bonus', 'penalty', 'custody_deficit', 'advance']);

        // المبلغ المالي
        $table->decimal('amount', 10, 2);

        // وصف العملية (مثال: "مكافأة الأداء المتميز" أو "عجز عهدة رقم #12")
        $table->string('description');

        // تاريخ الاستحقاق (متى سيتم تطبيقها على الراتب)
        $table->date('transaction_date');

        // ربط اختياري بالعهدة (لمعرفة المصدر إذا كان النوع عجز عهدة)
        $table->foreignId('custody_id')->nullable()->constrained()->onDelete('set null');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
