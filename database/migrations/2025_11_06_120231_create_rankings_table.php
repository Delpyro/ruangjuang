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
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke user (Diperbaiki)
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade'); // Jika user dihapus, rankingnya ikut terhapus

            // Relasi ke tryout
            $table->foreignId('tryout_id')
                  ->constrained('tryouts')
                  ->onDelete('cascade'); // Jika tryout dihapus, rankingnya ikut terhapus

            // Skor dari percobaan pertama
            $table->decimal('score', 8, 2)->default(0);

            $table->timestamps();

            // Kunci unik (Diperbaiki)
            $table->unique(['id_user', 'tryout_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};