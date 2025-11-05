<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->onDelete('cascade');

            $table->foreignId('fee_id')
                ->constrained('fees')
                ->onDelete('cascade');

            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // e.g. Cash, Transfer, POS
            $table->date('payment_date')->nullable();
            $table->string('term')->nullable(); // First Term, Second Term, etc.
            $table->string('session')->nullable(); // 2025/2026
            $table->string('reference')->nullable(); // receipt or transaction code
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
