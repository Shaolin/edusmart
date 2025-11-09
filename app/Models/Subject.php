<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    
    protected $fillable = ['name', 'level', 'school_id',];


    // A subject can be taught by many teachers across classes
    public function teachers()
    {
        return $this->belongsToMany(
            Teacher::class,
            'class_subject_teacher',
            'subject_id',
            'teacher_id'
        )->withPivot('class_id');
    }
     // A subject belongs to many classes (through pivot)
     public function classes()
     {
         return $this->belongsToMany(
             SchoolClass::class,
             'class_subject_teacher',
             'subject_id',
             'class_id'
         )->withPivot('teacher_id');
     }
     public function studentFees()
{
    return $this->hasMany(StudentFee::class);
}

public function results()
{
    return $this->hasMany(Result::class);
}
public function school()
    {
        return $this->belongsTo(School::class);
    }

}
