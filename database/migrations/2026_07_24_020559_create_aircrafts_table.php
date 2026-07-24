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
        Schema::create('aircrafts', function (Blueprint $table) {
            $table->id();
            $table->string('registration')->unique();
            $table->string('type');
            $table->string('model');
            $table->double('total_hours')->default(0.0);
            $table->double('hours_to_overhaul')->default(0.0);
            $table->foreignId('flying_id')->constrained('training_providers')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->string('status')->default('Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircrafts');
    }
};
