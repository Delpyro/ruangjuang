<?php
// database/migrations/2025_10_21_000000_add_discount_dates_to_tryouts_table.php

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
        Schema::table('tryouts', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->timestamp('discount_start_date')->nullable()->after('discount');
            $table->timestamp('discount_end_date')->nullable()->after('discount_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tryouts', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn('discount_end_date');
            $table->dropColumn('discount_start_date');
        });
    }
};