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
        // PENTING: Jika error log menyebut 'user_bundle_purchases',
        // pastikan nama tabel ini sesuai dengan yang diharapkan oleh Model Bundle Anda.
        // Asumsi nama tabel pivot: 'bundle_user'
        Schema::create('bundle_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('bundle_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->nullable();
            $table->timestamp('purchased_at')->nullable();
            
            // Kolom ini MESTI UNIK untuk mencegah pembelian ganda yang tidak diinginkan.
            $table->unique(['id_user', 'bundle_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_user');
    }
};
