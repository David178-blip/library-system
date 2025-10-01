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
    Schema::table('borrows', function (Blueprint $table) {
        $table->dateTime('borrowed_at')->nullable()->change();
        $table->dateTime('due_at')->nullable()->change();
        $table->dateTime('returned_at')->nullable()->change();
    });
}

public function down()
{
    Schema::table('borrows', function (Blueprint $table) {
        $table->dateTime('borrowed_at')->nullable(false)->change();
        $table->dateTime('due_at')->nullable(false)->change();
        $table->dateTime('returned_at')->nullable(false)->change();
    });
}

};
