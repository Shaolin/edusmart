<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the old unique index on 'name'
            $table->dropUnique('subjects_name_unique');

            // Add a composite unique index on name + level + school_id
            $table->unique(['name', 'level', ], 'subjects_name_level_school_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the new composite index
            $table->dropUnique('subjects_name_level_school_unique');

            // Restore the old unique index on 'name'
            $table->unique('name');
        });
    }
};
