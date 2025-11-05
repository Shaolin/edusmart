<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'session_id', 'is_active'];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
