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
        Schema::create('tryouts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->boolean('is_hots')->default(false);
            $table->integer('duration')->nullable();
            $table->longText('content');
            $table->text('quote')->nullable(); // Kolom quote yang ditambahkan
            $table->integer('price');
            $table->integer('discount')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk optimasi query
            $table->index('slug');
            $table->index('is_active');
            $table->index('is_hots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
