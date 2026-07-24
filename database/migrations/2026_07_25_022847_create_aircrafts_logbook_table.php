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
        Schema::create('aircrafts_logbook', function (Blueprint $table) {
            $table->id();
            $table->string('aircraft');
            $table->dateTime('date_time');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->integer('block_off_start');
            $table->integer('take_off');
            $table->integer('landing');
            $table->integer('block_on_off');
            $table->integer('block_time')->default(0);
            $table->integer('flight_time')->default(0);
            $table->decimal('fuel_used_gal', 8, 2)->default(0);
            $table->text('technical_issues')->nullable();
            $table->text('mechanics')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircrafts_logbook');
    }
};
