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
    Schema::create('email_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('type'); // reminder or overdue
        $table->string('book_title');
        $table->timestamp('sent_at')->useCurrent();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
