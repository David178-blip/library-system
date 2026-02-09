<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand enum to include all statuses used in the codebase
        DB::statement("ALTER TABLE borrows MODIFY COLUMN status ENUM('pending','borrowed','returned','overdue','lost','rejected') NOT NULL DEFAULT 'borrowed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum definition
        DB::statement("ALTER TABLE borrows MODIFY COLUMN status ENUM('borrowed','returned','overdue') NOT NULL DEFAULT 'borrowed'");
    }
};
