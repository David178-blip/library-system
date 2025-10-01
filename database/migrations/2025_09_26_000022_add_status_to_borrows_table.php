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
    Schema::table('borrows', function (Blueprint $table) {
        $table->enum('status', [
            'pending', 'approved', 'borrowed', 'returned', 'overdue', 'rejected'
        ])->default('pending')->change();
    });
}

public function down(): void
{
    Schema::table('borrows', function (Blueprint $table) {
        $table->enum('status', ['borrowed','returned','overdue'])->default('borrowed')->change();
    });
}

};
