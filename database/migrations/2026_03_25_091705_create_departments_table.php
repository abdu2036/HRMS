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
    Schema::create('departments', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // اسم القسم
        $table->string('code')->unique(); // كود القسم الفريد (مثل: ACC-01)
        $table->text('description')->nullable(); // وصف القسم
        
        // الربط الشجري (القسم الأب)
        $table->foreignId('parent_id')->nullable()->constrained('departments')->onDelete('restrict');
        
        // مدير القسم (يربط بجدول الموظفين مستقبلاً)
        $table->unsignedBigInteger('manager_id')->nullable(); 
        
        $table->integer('order')->default(0); // للترتيب من الأعلى للأقل سلطة
        $table->boolean('status')->default(true); // نشط أو غير نشط
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
