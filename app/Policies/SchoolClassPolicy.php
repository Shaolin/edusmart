<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SchoolClass;

class SchoolClassPolicy
{
    /**
     * Determine if the teacher can manage this class
     */
    public function manage(User $user, SchoolClass $class)
    {
        // Allow admin to access all classes
        if ($user->role === 'admin') {
            return true;
        }

        // Must be a teacher
        if ($user->role !== 'teacher') {
            return false;
        }

        // Check if teacher is the form teacher of this class
        return $user->teacher && $user->teacher->id === $class->form_teacher_id;
    }


}
