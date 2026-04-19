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
    Schema::create('custodies', function (Blueprint $table) {
        $table->id();
        // الربط مع الموظف
        $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
        
        $table->string('name'); // اسم العهدة
        $table->enum('type', ['financial', 'hardware']); // نوع العهدة
        $table->decimal('amount', 10, 2)->default(0); // القيمة المالية للمحاسبة
        
        // الحالة: (مستلمة، مرتجعة، عجز)
        $table->enum('status', ['received', 'returned', 'shortage'])->default('received');
        
        $table->decimal('shortage_amount', 10, 2)->default(0); // مبلغ العجز إن وجد
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custodies');
    }
};
