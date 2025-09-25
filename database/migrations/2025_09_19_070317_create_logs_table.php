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
       Schema::create('logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
    $table->string('action', 50);
    $table->string('table_name', 50);
    $table->unsignedBigInteger('record_id');
    $table->text('description')->nullable();
    $table->timestamp('created_at')->useCurrent();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
