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
        Schema::table('transactions', function (Blueprint $table) {
            // 1. Pastikan id_tryout dapat menerima NULL.
            // CATATAN: Langkah ini memerlukan paket 'doctrine/dbal' dan disarankan
            // dilakukan secara terpisah (drop foreign key -> change -> add foreign key).
            // Namun, untuk percobaan, kita tetap gunakan change()
            $table->foreignId('id_tryout')->nullable()->change(); 

            // 2. Tambahkan kolom id_bundle sebagai Foreign Key ke tabel 'bundles'.
            // Kolom ini harus nullable. Posisikan setelah id_tryout.
            $table->foreignId('id_bundle')
                  ->nullable()
                  ->constrained('bundles') // Nama tabel bundles
                  ->onDelete('SET NULL')
                  ->after('id_tryout'); // Posisikan setelah id_tryout

            // 3. (Opsional) Tambahkan index pada kolom baru
            $table->index('id_bundle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            
            // Hapus foreign key id_bundle
            $table->dropForeign(['id_bundle']); 
            
            // Hapus kolom id_bundle
            $table->dropColumn('id_bundle');

            // Kembalikan id_tryout ke status NOT NULL
            // Ini sangat penting jika status awalnya NOT NULL.
            $table->foreignId('id_tryout')->nullable(false)->change(); 
        });
    }
};
