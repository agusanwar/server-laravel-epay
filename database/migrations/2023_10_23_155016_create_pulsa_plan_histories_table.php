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
        Schema::create('pulsa_plan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pulsa_plan_id')->constrained('pulsa_plans');
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->string('phone_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulsa_plan_histories');
    }
};
