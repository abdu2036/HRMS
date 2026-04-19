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
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        
        // 1. الأكواد والتعريفات الرقمية
        $table->string('employee_code')->unique(); // رقم الموظف الإداري
        $table->string('fingerprint_code')->unique()->nullable(); // كود جهاز البصمة
        
        // 2. المعلومات الشخصية الأساسية
        $table->string('full_name');
        $table->enum('gender', ['male', 'female']);
        $table->date('date_of_birth')->nullable();
        $table->string('marital_status')->nullable(); // أعزب، متزوج، إلخ
        $table->string('qualification')->nullable(); // 🆕 المؤهل العلمي
        
        // 3. بيانات الاتصال والعنوان
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->text('address')->nullable();
        
        // 4. الثبوتيات الرسمية
        $table->string('national_id')->unique(); // الرقم الوطني / الإقامة
        $table->date('id_expiry_date')->nullable(); // 🆕 تاريخ انتهاء الهوية
        
        // 5. التبعية الإدارية (الروابط)
        $table->foreignId('department_id')->constrained('departments');
        $table->foreignId('job_title_id')->constrained('job_titles');
        $table->foreignId('shift_id')->constrained('shifts');
        
        // المدير المباشر (موظف مرتبط بموظف آخر)
        $table->unsignedBigInteger('manager_id')->nullable();
        $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');

        // 6. البيانات المالية
        $table->decimal('basic_salary', 10, 2); // 🆕 الراتب الأساسي الفعلي
        $table->string('iban')->nullable(); // 🆕 رقم الحساب البنكي لتحويل الرواتب
        
        // 7. التوظيف والحالة
        $table->date('hire_date'); // تاريخ المباشرة
        $table->date('leaving_date')->nullable(); // 🆕 تاريخ ترك العمل (للاستقالات)
        $table->string('employment_type')->default('full_time'); // دوام كامل، جزئي، تعاقد
        $table->boolean('status')->default(1); // 1: نشط، 0: متوقف
        
        // 8. الصور والمرفقات
        $table->string('profile_photo')->nullable(); // صورة الموظف
        $table->string('id_proof')->nullable(); // صورة الهوية أو جواز السفر
        // 9. ملاحظات عامة (الوصف) 👈 أضف هذا السطر هنا
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
