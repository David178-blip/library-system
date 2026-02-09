<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add accession_number as the main identifier for a book.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Add the column after isbn; adjust if your books table is different
            $table->string('accession_number')->nullable()->after('isbn');

            // Make accession_number unique so each book/copy has a distinct identifier
            $table->unique('accession_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // First drop the unique index, then the column
            $table->dropUnique(['accession_number']);
            $table->dropColumn('accession_number');
        });
    }
};