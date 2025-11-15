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
        Schema::create('users_answers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onDelete('cascade');
            
            $table->foreignId('answer_id')
                  ->nullable()
                  ->constrained('answers')
                  ->onDelete('set null');
            
            $table->integer('score')->default(0);
            $table->integer('attempt_count')->default(1); 
            
            $table->timestamps();

            $table->index(['id_user', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_answers');
    }
};