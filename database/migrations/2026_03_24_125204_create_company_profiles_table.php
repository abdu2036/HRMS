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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();

            // 1. اسم الشركة (إلزامي)
            $table->string('company_name')->comment('اسم الشركة الرسمي');

            // 2. عنوان الشركة (إلزامي)
            $table->text('address')->comment('عنوان مقر الشركة بالتفصيل');

            // 3. رقم هاتف الشركة (اختياري)
            $table->string('company_phone')->nullable()->comment('رقم هاتف التواصل الخاص بالشركة');

            // 4. البريد الإلكتروني للشركة (اختياري)
            $table->string('company_email')->nullable()->comment('البريد الإلكتروني الرسمي للمراسلات');

            // 5. الموقع الإلكتروني للشركة (اختياري)
            $table->string('website')->nullable()->comment('رابط الموقع الإلكتروني الرسمي');

            //شعار الشركة (اختياري)

            $table->string('company_logo')->nullable()->comment('مسار شعار الشركة');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
