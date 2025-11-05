<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'qualification',
        'specialization',
    ];

    // Each teacher belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A teacher can be a form teacher of many classes
    public function formClasses()
    {
        return $this->hasMany(SchoolClass::class, 'form_teacher_id');
    }

    // A teacher can teach many subjects across different classes
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject_teacher',
            'teacher_id',
            'subject_id'
        )->withPivot('class_id');
    }

    public function school()
{
    return $this->belongsTo(School::class);
}

}
