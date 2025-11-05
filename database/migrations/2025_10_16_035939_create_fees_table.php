<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('name'); // e.g., Tuition, PTA Levy, Uniform
            $table->decimal('amount', 10, 2);
            $table->string('term')->nullable(); // e.g., First Term, Second Term
            $table->year('session')->nullable(); // e.g., 2025
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
