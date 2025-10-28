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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reported_id');   // one user in the conversation
            $table->unsignedBigInteger('reported_by_id');   // the other user
            $table->string('message');
            $table->integer('is_processed')->default(0);
            $table->dateTime('processed_at')->nullable();
            $table->foreign('reported_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reported_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
