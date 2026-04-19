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
        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // مسمى الوظيفة (مثلاً: سكرتير)
            $table->text('description')->nullable(); // مهام الوظيفة
            $table->decimal('min_salary', 10, 2)->nullable(); // الحد الأدنى للراتب
            $table->decimal('max_salary', 10, 2)->nullable(); // الحد الأقصى للراتب
            $table->boolean('status')->default(true); // نشط / غير نشط
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_titles');
    }
};
