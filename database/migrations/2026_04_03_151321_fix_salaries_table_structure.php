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
    Schema::table('salaries', function (Blueprint $table) {
        // 1. حذف الربط القديم الخاطئ مع جدول users
        // ملاحظة: قد تحتاج للتأكد من اسم القيد في قاعدة البيانات لديك
        $table->dropForeign(['employee_id']); 

        // 2. ربط الحقل بجدول الموظفين الصحيح
        $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

        // 3. جعل تاريخ التفعيل اختيارياً أو له قيمة افتراضية لتجنب الخطأ
        $table->date('effective_date')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
