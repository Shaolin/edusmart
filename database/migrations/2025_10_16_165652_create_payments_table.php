<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_id')->constrained('fees')->onDelete('cascade');
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date')->default(now());
            $table->string('method')->nullable(); // e.g. Cash, Transfer, POS
            $table->enum('status', ['partial', 'paid'])->default('partial');
            $table->text('notes')->nullable(); // optional comment
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
