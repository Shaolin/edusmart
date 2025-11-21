<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {

            // 1. Drop the OLD global unique index if it still exists
            // MySQL usually names it "students_admission_number_unique"
            try {
                DB::statement('ALTER TABLE `students` DROP INDEX `students_admission_number_unique`');
            } catch (\Exception $e) {
                // Ignore if index does not exist
            }

            // 2. Add composite unique index for school-specific uniqueness
            // This means: admission_number must be unique *within each school*
            $table->unique(['school_id', 'admission_number'], 'students_school_admission_unique');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

            // Remove composite unique constraint
            try {
                $table->dropUnique('students_school_admission_unique');
            } catch (\Exception $e) {
                // Ignore
            }

            // Restore OLD global unique index (optional)
            try {
                DB::statement('ALTER TABLE `students` ADD UNIQUE `students_admission_number_unique` (`admission_number`)');
            } catch (\Exception $e) {
                // Ignore
            }
        });
    }
};
