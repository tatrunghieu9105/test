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
        Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
    $table->foreignId('showtime_id')->constrained('showtimes')->cascadeOnDelete()->cascadeOnUpdate();
    $table->foreignId('seat_id')->constrained('seats')->cascadeOnDelete()->cascadeOnUpdate();
    $table->foreignId('combo_id')->nullable()->constrained('combos')->nullOnDelete()->cascadeOnUpdate();
    $table->foreignId('discount_code_id')->nullable()->constrained('discount_codes')->nullOnDelete()->cascadeOnUpdate();
    $table->decimal('price', 10, 2);
    $table->enum('status', [
        'pending', 'paid', 'used',
        'pending_cash', 'paid_cash',
        'pending_online', 'paid_online',
        'cancelled'
    ])->default('pending');
    $table->timestamps();
            $table->softDeletes();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
