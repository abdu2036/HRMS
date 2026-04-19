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
    Schema::table('job_titles', function (Blueprint $table) {
        // إضافة حقل القسم كـ Foreign Key
        // nullable() تسمح بوجود مسميات قديمة بدون قسم مؤقتاً
        // constrained() تربطه تلقائياً بجدول departments
        $table->foreignId('department_id')->nullable()->after('id')->constrained('departments')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('job_titles', function (Blueprint $table) {
        $table->dropForeign(['department_id']);
        $table->dropColumn('department_id');
    });
}
};