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
        $table->date('health_certificate_expiry')->nullable()->after('id_expiry_date'); // تاريخ الانتهاء
        $table->string('health_certificate_file')->nullable()->after('id_proof'); // مسار المرفق
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
