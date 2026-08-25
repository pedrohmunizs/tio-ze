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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_route')->constrained('routes')->onDelete('cascade');
            $table->foreignId('fk_vehicle')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('fk_driver')->constrained('drivers')->onDelete('cascade');
            $table->date('date');
            $table->enum('type', ['GOING', 'RETURNING'])->default('GOING');
            $table->enum('status', ['SCHEDULED', 'STARTED', 'PAUSED', 'COMPLETED', 'CANCELLED'])->default('SCHEDULED');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
