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
    Schema::create('loans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
        $table->decimal('amount', 10, 2); // إجمالي مبلغ السلفة
        $table->decimal('installment', 10, 2); // القسط الشهري الذي سيخصم
        $table->decimal('remaining_amount', 10, 2); // المبلغ المتبقي (ينقص تلقائياً)
        $table->date('start_date'); // تاريخ بدء الخصم
        $table->enum('status', ['active', 'paid'])->default('active'); // حالة السلفة
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
