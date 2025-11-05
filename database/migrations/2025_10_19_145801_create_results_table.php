<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');

            $table->decimal('test_score', 5, 2)->default(0);
            $table->decimal('exam_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->string('grade')->nullable();
            $table->string('remark')->nullable();

            $table->timestamps();

            // Prevent duplicate entries for same student, subject, term, and session
            $table->unique(['student_id', 'subject_id', 'term_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
