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
        Schema::create('credits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('credit_ref' )->unique();
            $table->string('borrower_name')->nullable();
            $table->string('product_key')->nullable();
            $table->jsonb('product_parameters')->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamp('credited_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->integer('initial_principal')->nullable();
            $table->integer('remaining_principal')->nullable();
            $table->timestamp('settled_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
