<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Student who made the payment
            $table->foreignId('student_id')->constrained()->onDelete('cascade');

            // Fee being paid for (e.g. Tuition Fee, Uniform)
            $table->foreignId('fee_id')->constrained('fees')->onDelete('cascade');

            // Payment details
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('balance_after_payment', 10, 2)->default(0);
            $table->string('payment_method')->nullable(); // Cash, Transfer, POS, etc.
            $table->date('payment_date')->default(now());

            // Session & term tracking
            $table->string('session')->nullable(); // e.g. 2025/2026
            $table->string('term')->nullable();    // e.g. First Term

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
