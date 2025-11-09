<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to guardians table only if missing
        if (!Schema::hasColumn('guardians', 'school_id')) {
            Schema::table('guardians', function (Blueprint $table) {
                $table->foreignId('school_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('schools')
                      ->nullOnDelete();
            });
        }

        // Add to fees table only if missing
        if (!Schema::hasColumn('fees', 'school_id')) {
            Schema::table('fees', function (Blueprint $table) {
                $table->foreignId('school_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('schools')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('guardians', 'school_id')) {
            Schema::table('guardians', function (Blueprint $table) {
                $table->dropConstrainedForeignId('school_id');
            });
        }

        if (Schema::hasColumn('fees', 'school_id')) {
            Schema::table('fees', function (Blueprint $table) {
                $table->dropConstrainedForeignId('school_id');
            });
        }
    }
};
