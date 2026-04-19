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
    Schema::table('employees', function (Blueprint $table) {
        // إضافة الحقل وربطه بجدول الفروع كعلاقة منظمة
        $table->foreignId('branch_id')->nullable()->after('employee_code')->constrained('branches')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('employees', function (Blueprint $table) {
        $table->dropForeign(['branch_id']);
        $table->dropColumn('branch_id');
    });
}
};
