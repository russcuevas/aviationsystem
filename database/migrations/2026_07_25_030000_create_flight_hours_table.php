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
        Schema::create('flight_hours', function (Blueprint $table) {
            $table->id();
            $table->string('log_id')->unique();
            $table->date('date')->nullable();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->onDelete('cascade');
            $table->foreignId('aircraft_id')->constrained('aircrafts')->onDelete('cascade');
            $table->foreignId('stage_id')->nullable()->constrained('students_staging')->onDelete('set null');
            $table->string('lesson')->nullable();
            $table->decimal('dual_instruction_time', 8, 2)->nullable();
            $table->decimal('pic_time', 8, 2)->nullable();
            $table->decimal('solo_time', 8, 2)->nullable();
            $table->decimal('instrument_flight_time', 8, 2)->nullable();
            $table->decimal('total_time', 8, 2)->default(0.00);
            $table->string('status')->default('pending review');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_hours');
    }
};
