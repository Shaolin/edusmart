<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_attendance_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('session_id')
                ->constrained('sessions')
                ->cascadeOnDelete();

            $table->foreignId('term_id')
                ->constrained('terms')
                ->cascadeOnDelete();

            $table->unsignedInteger('school_opened')->nullable();
            $table->unsignedInteger('times_present')->nullable();
            $table->unsignedInteger('times_absent')->nullable();

            $table->timestamps();

            $table->unique([
                'student_id',
                'session_id',
                'term_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_attendance_summaries');
    }
};