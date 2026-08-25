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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_provider')->constrained('providers')->onDelete('cascade');
            $table->foreignId('fk_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('fk_contract')->constrained('contracts')->onDelete('cascade')->unique();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['fk_provider', 'fk_user']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
