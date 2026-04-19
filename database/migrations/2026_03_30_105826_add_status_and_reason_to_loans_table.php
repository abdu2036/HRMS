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
    Schema::table('loans', function (Blueprint $table) {
        // تغيير نوع الحالة ليشمل الطلبات الجديدة
        $table->string('status')->default('pending')->change(); 
        $table->text('reason')->nullable(); // سبب طلب السلفة
        $table->text('admin_reply')->nullable(); // رد المدير
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
        });
    }
};
