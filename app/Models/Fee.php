<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'name',
        'amount',
        'term',
        'session',
        'school_id',
    ];

    // protected $fillable = [
    //     'class_id',
    //     'name',
    //     'amount',
    //     'term',         
    //     'session',      
    //     'session_id',   
    //     'term_id',      
    //     'school_id',
    // ];

    // Relationship: belongs to a class
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Relationship: has many student fee records
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }
    
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
