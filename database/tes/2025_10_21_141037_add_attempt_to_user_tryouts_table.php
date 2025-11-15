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
        Schema::table('user_tryouts', function (Blueprint $table) {
            // Menambahkan kolom attempt
            // unsignedTinyInteger cocok untuk angka kecil seperti 1 sampai 3
            $table->unsignedTinyInteger('attempt')->default(1)->after('tryout_id');
            
            // Menambahkan unique constraint baru yang mencakup attempt
            // Ini memastikan satu user hanya bisa memiliki 3 baris (attempt 1, 2, 3) untuk tryout yang sama.
            $table->unique(['id_user', 'tryout_id', 'attempt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tryouts', function (Blueprint $table) {
            $table->dropUnique(['id_user', 'tryout_id', 'attempt']);
            $table->dropColumn('attempt');
        });
    }
};