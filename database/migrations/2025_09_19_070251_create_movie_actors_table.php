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
        Schema::create('movie_actors', function (Blueprint $table) {
    $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete()->cascadeOnUpdate();
    $table->foreignId('actor_id')->constrained('actors')->cascadeOnDelete()->cascadeOnUpdate();
    $table->primary(['movie_id', 'actor_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_actors');
    }
};
