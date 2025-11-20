<?php

namespace App\Services;

use App\Mail\StudentResultMail;
use Illuminate\Support\Facades\Mail;

class ResultService
{
    public function sendStudentResult($student)
    {
        $results = $student->results;
        $school = $student->school;
        $term   = $student->current_term ?? null;
        $session = $student->current_session ?? null;

        // Send email
        Mail::to($student->parent_email)->send(
            new StudentResultMail($student, $results, $school, $term, $session)
        );

        return true;
    }
}
