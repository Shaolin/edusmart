<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Add school_id as nullable first to prevent errors with existing data
            $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('cascade');
        });

        // Optional: if you want to make it not nullable later, you can update after assigning school ids
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
