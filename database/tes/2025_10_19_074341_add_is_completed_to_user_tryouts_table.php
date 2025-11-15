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
            // Tambahkan kolom is_completed setelah ended_at
            $table->boolean('is_completed')->default(false)->after('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tryouts', function (Blueprint $table) {
            $table->dropColumn('is_completed');
        });
    }
};