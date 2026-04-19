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
    Schema::table('attendances', function (Blueprint $table) {
        // تخزين خطوط الطول والعرض بدقة عالية (Decimal)
        $table->decimal('lat', 10, 8)->nullable()->after('note');
        $table->decimal('lng', 11, 8)->nullable()->after('lat');
        
        // تخزين المسافة بين الموظف والشركة (بالمتار) لغرض التقارير
        $table->integer('distance_from_branch')->nullable()->after('lng');
    });
}

public function down(): void
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->dropColumn(['lat', 'lng', 'distance_from_branch']);
    });
}
};
