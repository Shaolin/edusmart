<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'student_id',
        'fee_id',
        'amount',
        'balance_after_payment', 
        'payment_method',
        'payment_date',
        'term',
        'session',
        'reference',
        'notes',
    ];
    

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
    public function class()
{
    return $this->belongsTo(SchoolClass::class, 'class_id');
}

public function school()
{
    return $this->belongsTo(School::class);
}


}
