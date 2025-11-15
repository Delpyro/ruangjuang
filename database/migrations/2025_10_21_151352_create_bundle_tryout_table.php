<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundle_tryout', function (Blueprint $table) {
            // Relasi ke tabel bundles
            $table->foreignId('bundle_id')->constrained('bundles')->onDelete('cascade');
            // Relasi ke tabel tryouts
            $table->foreignId('tryout_id')->constrained('tryouts')->onDelete('cascade');
            
            // Primary key gabungan
            $table->primary(['bundle_id', 'tryout_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_tryout');
    }
};