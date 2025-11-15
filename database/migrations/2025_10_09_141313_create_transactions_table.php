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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_tryout')->constrained('tryouts')->onDelete('cascade');
            $table->string('transaction_id');
            $table->string('order_id')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->integer('amount');
            $table->string('payment_method');
            $table->string('status')->default('pending');
            $table->string('ip_user');
            $table->string('payment_type')->nullable();
            $table->string('fraud_status')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('transaction_id');
            $table->index('order_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};