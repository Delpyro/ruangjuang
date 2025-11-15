<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Perintah ini akan mengubah tipe kolom 'answer' menjadi TEXT
        Schema::table('answers', function (Blueprint $table) {
            $table->text('answer')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Ini untuk mengembalikan ke varchar(255) jika Anda perlu rollback
        Schema::table('answers', function (Blueprint $table) {
            $table->string('answer', 255)->change();
        });
    }
};