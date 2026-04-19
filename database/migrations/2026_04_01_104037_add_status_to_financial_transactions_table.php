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
    Schema::table('financial_transactions', function (Blueprint $table) {
        // إضافة حالة العملية: pending (قيد الانتظار) أو paid (تم صرفها/خصمها)
        $table->enum('status', ['pending', 'paid'])->default('pending')->after('amount');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            //
        });
    }
};
