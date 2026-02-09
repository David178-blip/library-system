<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Any borrow that is approved, not yet returned, and not already
        // marked as an active/overdue loan should be marked as "borrowed".
        // This makes it match the reminder queries that look for status = 'borrowed'.
        DB::table('borrows')
            ->whereNull('returned_at')
            ->where('approval', 'approved')
            ->whereNotIn('status', ['borrowed', 'overdue'])
            ->update(['status' => 'borrowed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't reliably restore the previous status values,
        // so this migration is effectively irreversible.
        // Leaving this intentionally empty.
    }
};
