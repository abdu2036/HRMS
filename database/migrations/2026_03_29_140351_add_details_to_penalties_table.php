<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 
public function up() {
    Schema::table('penalties', function (Blueprint $table) {
        $table->decimal('amount', 8, 2)->default(0);
        $table->integer('days_count')->default(0);
        $table->text('description');
        $table->date('date');
        $table->string('type')->default('violation'); // مخالفة، غياب، إلخ
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            //
        });
    }
};
