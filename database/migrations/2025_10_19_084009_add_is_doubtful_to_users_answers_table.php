<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users_answers', function (Blueprint $table) {
            // Tambahkan kolom is_doubtful setelah answer_id
            $table->boolean('is_doubtful')->default(false)->after('answer_id');
        });
    }

    public function down(): void
    {
        Schema::table('users_answers', function (Blueprint $table) {
            $table->dropColumn('is_doubtful');
        });
    }
};