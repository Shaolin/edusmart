
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Remove the old unique constraint
            $table->dropUnique('teachers_staff_id_unique');

            // Add school_id if not exists
            if (!Schema::hasColumn('teachers', 'school_id')) {
                $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete()->after('user_id');
            }

            // Add composite unique: staff_id + school_id
            $table->unique(['school_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Drop composite unique
            $table->dropUnique(['school_id', 'staff_id']);

            // Restore unique on staff_id only
            $table->unique('staff_id');

            // Optionally remove school_id if added by this migration
            if (Schema::hasColumn('teachers', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }
        });
    }
};
