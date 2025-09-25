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
       Schema::create('movies', function (Blueprint $table) {
    $table->id();
    $table->string('title', 150);
    $table->text('description')->nullable();
    $table->string('poster_url')->nullable();
    $table->string('trailer_url')->nullable();
    $table->integer('duration');
    $table->date('release_date')->nullable();
    $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->cascadeOnUpdate();
    $table->timestamps();
            $table->softDeletes();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
