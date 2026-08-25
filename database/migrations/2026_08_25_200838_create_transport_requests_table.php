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
        Schema::create('transport_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_student')->constrained('children')->onDelete('cascade');
            $table->foreignId('fk_route')->constrained('routes')->onDelete('cascade');
            $table->foreignId('fk_provider')->constrained('providers')->onDelete('cascade');
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED', 'CANCELLED'])->default('PENDING');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['fk_provider', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_requests');
    }
};
