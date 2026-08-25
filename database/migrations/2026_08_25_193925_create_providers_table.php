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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_user')->constrained('users')->onDelete('cascade')->unique();
            $table->foreignId('fk_address')->constrained('addresses')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'blocked'])->default('pending');
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
