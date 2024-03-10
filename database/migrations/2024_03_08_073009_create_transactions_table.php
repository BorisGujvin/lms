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
            $table->uuid('id')->primary();
            $table->uuid('credit_id')->nullable();
            $table->string('transaction_key')->unique();
            $table->integer('sequence')->nullable();
            $table->string('hash', 50)->nullable();
            $table->uuid('reverse_transaction_id')->nullable();

            $table->timestamps();

            $table->foreign('credit_id')->references('id')->on('credits');
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
