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
    Schema::table('custodies', function (Blueprint $table) {
        // نغير العمود ليقبل الخيار الثالث "both"
        $table->enum('type', ['hardware', 'financial', 'both'])->change();
    });
}

public function down(): void
{
    Schema::table('custodies', function (Blueprint $table) {
        $table->enum('type', ['hardware', 'financial'])->change();
    });
}
};
