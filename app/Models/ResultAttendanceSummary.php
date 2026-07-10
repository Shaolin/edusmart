<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultAttendanceSummary extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'session_id',
        'term_id',
        'school_opened',
        'times_present',
        'times_absent',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}