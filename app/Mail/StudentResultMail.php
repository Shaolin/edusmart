<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $results;
    public $school;
    public $term;
    public $session;

    public function __construct($student, $results, $school, $term, $session)
    {
        $this->student = $student;
        $this->results = $results;
        $this->school = $school;
        $this->term = $term;
        $this->session = $session;
    }

    public function build()
    {
        return $this->subject("Your Child's Result")
                    ->view('emails.student_result');
    }
}
