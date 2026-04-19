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
    Schema::table('branches', function (Blueprint $table) {
        // إحداثيات الفرع (الموقع الرسمي للشركة)
        $table->decimal('latitude', 10, 8)->nullable();
        $table->decimal('longitude', 11, 8)->nullable();
        
        // مسافة السماح بالامتار (مثلاً 50 متر حول الشركة)
        $table->integer('radius_meters')->default(100); 
    });
}

public function down(): void
{
    Schema::table('branches', function (Blueprint $table) {
        $table->dropColumn(['latitude', 'longitude', 'radius_meters']);
    });
}
};
