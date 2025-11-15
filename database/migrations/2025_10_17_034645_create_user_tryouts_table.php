<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tryouts', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('tryout_id')->constrained('tryouts')->onDelete('cascade');
            
            // Kolom Attempt
            $table->unsignedTinyInteger('attempt')->default(1);
            
            // Kolom Transaksi (Non-Unique Order ID)
            $table->string('order_id');
            $table->timestamp('purchased_at');
            
            // Kolom Status Pengerjaan
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('is_completed')->default(false);

            $table->timestamps();

            // Kunci Unik Final yang BENAR: (user, tryout, attempt)
            $table->unique(['id_user', 'tryout_id', 'attempt'], 'user_tryouts_unique_attempt');
            
            // Index Non-Unique untuk order_id
            $table->index('order_id', 'user_tryouts_order_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tryouts');
    }
};