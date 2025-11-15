<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('users_answers', function (Blueprint $table) {
            $table->dropColumn('attempt_count');
        });

        Schema::table('users_answers', function (Blueprint $table) {
            $table->foreignId('user_tryout_id')
                  ->after('id_user') 
                  ->constrained('user_tryouts')
                  ->onDelete('cascade');

            $table->unique(['user_tryout_id', 'question_id']);
        });
    }

    
    public function down(): void
    {
        Schema::table('users_answers', function (Blueprint $table) {
            $table->dropUnique(['user_tryout_id', 'question_id']);
            $table->dropForeign(['user_tryout_id']);
            $table->dropColumn('user_tryout_id');
            
            $table->integer('attempt_count')->default(1);
        });
    }
};