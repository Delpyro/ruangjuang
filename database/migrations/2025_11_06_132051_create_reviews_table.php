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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // Relasi (Gunakan id_user agar konsisten)
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('tryout_id')->constrained('tryouts')->onDelete('cascade');

            // Isi Ulasan
            $table->tinyInteger('rating')->unsigned()->nullable(); // Rating 1-5
            $table->text('review_text')->nullable(); // Teks ulasan
            
            // Status (Opsional tapi bagus)
            $table->boolean('is_published')->default(false); // Admin bisa setujui ini

            $table->timestamps();

            // User hanya bisa review 1 tryout 1 kali
            $table->unique(['id_user', 'tryout_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};