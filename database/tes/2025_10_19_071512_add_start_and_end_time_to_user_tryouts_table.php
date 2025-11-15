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
            // Menambahkan kolom setelah 'purchased_at' agar rapi
            $table->timestamp('started_at')->nullable()->after('purchased_at');
            $table->timestamp('ended_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tryouts', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'ended_at']);
        });
    }
};