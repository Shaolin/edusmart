<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'student_id',
        'teacher_id',
        'class_id',
        'school_id',
        'date',
        'status',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Optional: Global scope for multi-tenancy safety
     * (keeps attendance automatically scoped to school)
     */
    protected static function booted()
    {
        static::addGlobalScope('school', function ($query) {
            if (auth()->check()) {
                $query->where('school_id', auth()->user()->school_id);
            }
        });
    }
}
