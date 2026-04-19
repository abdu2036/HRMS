<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. تعطيل فحص القيود مؤقتاً لتجنب الأخطاء أثناء التعديل
        Schema::disableForeignKeyConstraints();

        // 2. محاولة حذف القيد القديم (بواسطة SQL مباشر لتجنب حاجة Doctrine)
        try {
            DB::statement('ALTER TABLE loans DROP FOREIGN KEY loans_employee_id_foreign');
        } catch (\Exception $e) {
            // إذا لم يجد القيد بهذا الاسم، سيتجاهل الخطأ ويكمل
        }

        // 3. تنظيف البيانات: حذف السلف التي لا تملك موظفاً حقيقياً في جدول employees
        DB::statement("DELETE FROM loans WHERE employee_id NOT IN (SELECT id FROM employees)");

        // 4. إنشاء القيد الجديد الصحيح المرتبط بجدول الموظفين
        Schema::table('loans', function (Blueprint $table) {
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');
        });

        // 5. إعادة تفعيل فحص القيود
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
    }
};