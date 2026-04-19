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
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        
        // الربط مع جدول الموظفين الجديد (employees)
        $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

        $table->date('date'); 
        $table->time('signin_time')->nullable(); 
        $table->time('signout_time')->nullable(); 
        $table->string('status')->default('absent'); 
        $table->integer('late_minutes')->default(0); 
        $table->integer('overtime_minutes')->default(0); 
        $table->text('note')->nullable(); 
        $table->timestamps();

        // منع التكرار للموظف في نفس اليوم
        $table->unique(['employee_id', 'date']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
