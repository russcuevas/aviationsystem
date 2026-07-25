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
        Schema::create('grade_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('sheet_id')->unique();
            $table->date('date');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->json('lesson_grades')->nullable();
            $table->decimal('total_score', 5, 2)->default(0.00);
            $table->string('overall_grade')->default('N/A');
            $table->string('status')->default('For Review');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('set null');
            $table->foreign('stage_id')->references('id')->on('students_staging')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_sheets');
    }
};
