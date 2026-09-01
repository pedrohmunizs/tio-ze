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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_student')->constrained('children')->onDelete('cascade');
            $table->foreignId('fk_route')->constrained('routes')->onDelete('cascade');
            $table->foreignId('fk_provider')->constrained('providers')->onDelete('cascade');
            $table->foreignId('fk_transport_request')->constrained('transport_requests')->onDelete('cascade')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
