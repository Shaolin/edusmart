<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'relationship',
        'school_id',
    ];

    // Guardian has many students
    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }
    
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
