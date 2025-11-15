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
        Schema::create('tryout_category_scores', function (Blueprint $table) {
            $table->id();

            // Kunci utama yang menghubungkan ke pengerjaan spesifik
            // Dari sini kita bisa tahu: id_user, tryout_id, dan attempt
            $table->foreignId('user_tryout_id')
                  ->constrained('user_tryouts') // Asumsi tabel Anda namanya 'user_tryouts'
                  ->onDelete('cascade');

            // Kunci yang menghubungkan ke subtes/kategori
            $table->foreignId('question_category_id')
                  ->constrained('question_categories') // Asumsi tabel Anda 'question_categories'
                  ->onDelete('cascade');
            
            // Data statistik untuk rapor
            $table->decimal('score', 8, 2)->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('wrong_count')->default(0);       // Yang dijawab salah
            $table->integer('unanswered_count')->default(0); // Yang dikosongkan
            $table->integer('total_questions')->default(0);

            $table->timestamps();

            // Memastikan data unik: 
            // Satu pengerjaan (user_tryout_id) hanya punya satu data per kategori.
            $table->unique(['user_tryout_id', 'question_category_id'], 'user_tryout_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_category_scores');
    }
};