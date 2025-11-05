<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Add the level column if it doesn’t exist yet
            if (!Schema::hasColumn('subjects', 'level')) {
                $table->enum('level', ['Nursery', 'Primary', 'JSS', 'SSS'])->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
