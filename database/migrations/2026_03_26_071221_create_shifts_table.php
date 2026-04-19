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
    Schema::create('shifts', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // مثال: شفت صباحي، شفت مسائي
        $table->time('start_time'); // وقت الحضور
        $table->time('end_time');   // وقت الانصراف
        $table->integer('break_duration')->default(0); // مدة الاستراحة بالدقائق (اختياري)
        $table->boolean('status')->default(1); // نشط أو متوقف
       $table->text('description')->nullable(); // وصف القسم

        $table->timestamps();
    });
}
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
