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
    Schema::create('early_exit_permissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->date('date'); // التاريخ المسموح فيه بالخروج
        $table->time('allowed_exit_time'); // الوقت الذي حدده المدير (مثلاً 07:00:00)
        $table->text('reason')->nullable(); // سبب الإذن
        $table->foreignId('created_by')->constrained('users'); // معرف المدير الذي أنشأ الإذن
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('early_exit_permissions');
    }
};
