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
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_route')->constrained('routes')->onDelete('cascade');
            $table->foreignId('fk_address')->constrained('addresses')->onDelete('cascade');
            $table->integer('stop_order');
            $table->enum('type', ['going', 'returning']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stops');
    }
};
