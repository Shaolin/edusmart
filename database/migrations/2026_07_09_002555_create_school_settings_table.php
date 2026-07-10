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
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();

            // Each school has one settings record
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            // Academic Information
            $table->date('next_term_begins')->nullable();
            $table->decimal('next_term_school_fees', 12, 2)->nullable();

            $table->timestamps();

            // Ensure only one settings record per school
            $table->unique('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};