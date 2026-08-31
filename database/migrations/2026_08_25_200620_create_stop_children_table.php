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
        Schema::create('stop_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_stop')->constrained('stops')->onDelete('cascade');
            $table->foreignId('fk_child')->constrained('children')->onDelete('cascade');
            $table->integer('stop_order');
            $table->enum('type', ['PICKUP', 'DROPOFF']);
            $table->timestamps();

            $table->unique(['fk_stop', 'fk_child']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stop_children');
    }
};
