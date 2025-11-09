<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name', 'section', 'form_teacher_id', 'next_class_id', 'school_id',
    ];

    // Class has many students
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    // Class belongs to a form teacher
    public function formTeacher()
    {
        return $this->belongsTo(Teacher::class, 'form_teacher_id');
    }

    // Class has many subjects, taught by different teachers
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject_teacher',
            'class_id',
            'subject_id'
        )->withPivot('teacher_id');
    }

    public function nextClass()
    {
        return $this->belongsTo(SchoolClass::class, 'next_class_id');
    }

    public function previousClasses()
    {
        return $this->hasMany(SchoolClass::class, 'next_class_id');
    }

    public function fees()
    {
        return $this->hasMany(Fee::class, 'class_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
