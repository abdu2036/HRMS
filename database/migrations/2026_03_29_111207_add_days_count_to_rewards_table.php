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
    Schema::table('rewards', function (Blueprint $table) {
        // إضافة عمود عدد الأيام مع جعل القيمة الافتراضية 0
        $table->integer('days_count')->default(0)->after('amount');
        
        // سنحتاج أيضاً لحقل الوصف (نص حر لسبب المكافأة) إذا لم يكن موجوداً
        if (!Schema::hasColumn('rewards', 'description')) {
            $table->text('description')->nullable()->after('days_count');
        }
    });
}

public function down()
{
    Schema::table('rewards', function (Blueprint $table) {
        $table->dropColumn(['days_count', 'description']);
    });
}
};