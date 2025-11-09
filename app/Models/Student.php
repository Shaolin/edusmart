<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_number',
        'name',
        'gender',
        'date_of_birth',
        'class_id',
        'guardian_id',
        'school_id',
    ];

    // Student belongs to a class
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Student belongs to a guardian
    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }
    public function feePayments()
{
    return $this->hasMany(FeePayment::class, 'student_id');
}
public function school()
{
    return $this->belongsTo(School::class);
}

public function results()
{
    return $this->hasMany(Result::class);
}





}
