<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->timestamp('expired_at')->nullable()->after('discount');
            $table->index(['expired_at', 'is_active']); // Composite index untuk query yang lebih efisien
        });
    }

    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->dropIndex(['expired_at', 'is_active']);
            $table->dropColumn('expired_at');
        });
    }
};