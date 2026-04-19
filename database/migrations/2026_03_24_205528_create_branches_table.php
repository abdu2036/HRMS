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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            // اسم الفرع (مثلاً: فرع طرابلس، فرع الخمس)
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // ربط الفرع بالشركة الأم - مهم جداً للتقارير لاحقاً
            $table->foreignId('company_profile_id')
                ->constrained('company_profiles')
                ->onDelete('cascade'); // إذا حذفت الشركة تُحذف فروعها تلقائياً

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
