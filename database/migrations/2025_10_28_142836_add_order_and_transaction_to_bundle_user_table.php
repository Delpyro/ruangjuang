<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Periksa apakah tabel bundle_user sudah ada sebelum mencoba memodifikasinya
        if (Schema::hasTable('bundle_user')) {
            Schema::table('bundle_user', function (Blueprint $table) {
                // Tambahkan kolom yang hilang yang menyebabkan error SQL di log Anda
                // order_id digunakan oleh Midtrans dan relasi belongsToMany.
                if (!Schema::hasColumn('bundle_user', 'order_id')) {
                    $table->string('order_id')->nullable()->after('bundle_id');
                }
                
                // Tambahkan atau pastikan transaction_id yang dipakai di relasi juga ada
                // (Ini opsional, tergantung apakah Anda ingin menyimpan ID Transaksi lokal di pivot)
                // Jika sudah ada dan bukan string, Anda mungkin perlu mengubahnya.
                if (!Schema::hasColumn('bundle_user', 'transaction_id')) {
                    $table->string('transaction_id')->nullable()->after('order_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('bundle_user', function (Blueprint $table) {
            if (Schema::hasColumn('bundle_user', 'order_id')) {
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('bundle_user', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
        });
    }
};