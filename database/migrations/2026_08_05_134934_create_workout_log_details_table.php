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
        Schema::create('workout_log_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_log_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->integer('sets_completed')->default(0);
            $table->integer('repetitions_completed')->default(0);
            $table->decimal('weight_used', 8, 2)->nullable();
            $table->integer('rest_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_log_details');
    }
};