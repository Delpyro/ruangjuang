<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_question')->constrained('questions')->onDelete('cascade');
            $table->string('answer');
            $table->string('image')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('points')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('id_question');
            $table->index('is_correct');
            $table->index('points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};