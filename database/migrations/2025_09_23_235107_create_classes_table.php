<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // e.g., "JSS1"
            $table->string('section')->nullable(); // e.g., "A", "B"

            // Form Teacher (points to teachers table, not users)
            $table->foreignId('form_teacher_id')
                  ->nullable()
                  ->constrained('teachers')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
