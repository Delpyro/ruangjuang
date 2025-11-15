<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tryout')->constrained('tryouts')->onDelete('cascade');
            $table->foreignId('id_question_categories')->nullable()->constrained('question_categories')->onDelete('set null');
            $table->foreignId('id_question_sub_category')->nullable()->constrained('question_sub_categories')->onDelete('set null');
            $table->longText('question');
            $table->string('image')->nullable();
            $table->longText('explanation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('id_tryout');
            $table->index('id_question_categories');
            $table->index('id_question_sub_category');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};