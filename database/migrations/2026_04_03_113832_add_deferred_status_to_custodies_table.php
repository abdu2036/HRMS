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
    Schema::table('custodies', function (Blueprint $table) {
        // إذا كان العمود موجوداً كـ string سنقوم فقط بتحديث التعليق أو التأكد منه
        // وإذا كنت تستخدم enum، يفضل تحويله لـ string ليكون أكثر مرونة مع الحالات الجديدة
        $table->string('status')->default('received')->change(); 
    });
}

public function down()
{
    Schema::table('custodies', function (Blueprint $table) {
        // في حال التراجع عن الميجريشن
    });
}
};
