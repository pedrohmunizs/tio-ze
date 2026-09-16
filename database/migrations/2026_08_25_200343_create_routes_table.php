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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_school')->constrained('schools')->onDelete('cascade');
            $table->foreignId('fk_provider')->constrained('providers')->onDelete('cascade');
            $table->foreignId('fk_driver')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('fk_vehicle')->constrained('vehicles')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->time('going_time');
            $table->time('returning_time');
            $table->string('days_of_week');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['fk_provider', 'fk_driver', 'fk_school', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
