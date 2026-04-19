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
    Schema::create('salaries', function (Blueprint $table) {
        $table->id();
        // ربط الراتب بالموظف (مع الحذف التلقائي إذا حذف الموظف)
        $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
        
        // الراتب الأساسي (استخدام decimal بدقة عالية للعملات)
        $table->decimal('basic_salary', 10, 2)->default(0);
        
        // إجمالي البدلات الثابتة (سكن، مواصلات، إلخ)
        $table->decimal('allowances', 10, 2)->default(0);
        
        // تاريخ بدء العمل بهذا الراتب (مهم للأرشفة والزيادات)
        $table->date('effective_date');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
