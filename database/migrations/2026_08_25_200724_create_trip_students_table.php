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
        Schema::create('trip_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_trip')->constrained('trips')->onDelete('cascade');
            $table->foreignId('fk_student')->constrained('children')->onDelete('cascade');
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('dropoff_time')->nullable();
            $table->enum('status', ['PENDING', 'PICKED_UP', 'DROPPED_OFF', 'ABSENT'])->default('PENDING');
            $table->timestamps();

            $table->unique(['fk_trip', 'fk_student']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_students');
    }
};
