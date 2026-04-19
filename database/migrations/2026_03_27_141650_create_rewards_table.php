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
    Schema::create('rewards', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained()->onDelete('cascade'); // ربط بالموظف
        $table->date('date');           // تاريخ المكافأة
        $table->string('type');         // نوع المكافأة (مثلاً: تميز، انضباط)
        $table->decimal('amount', 10, 2); // مبلغ المكافأة
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
