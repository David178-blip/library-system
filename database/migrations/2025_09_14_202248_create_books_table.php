<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->integer('year')->nullable();
            $table->string('isbn')->unique();
            $table->integer('copies')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('books');
    }
};
