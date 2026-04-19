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
        // نعدل العمود لإضافة الحالة الجديدة
        $table->enum('status', ['received', 'returned', 'shortage', 'settled'])
              ->default('received')
              ->change();
    });
}

public function down()
{
    Schema::table('custodies', function (Blueprint $table) {
        // نرجعه كما كان في حال أردنا التراجع عن التعديل
        $table->enum('status', ['received', 'returned', 'shortage'])
              ->default('received')
              ->change();
    });
}
};