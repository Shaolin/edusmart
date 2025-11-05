<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    
    protected $table = 'sessions';

    protected $fillable = ['name', 'is_active'];

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
