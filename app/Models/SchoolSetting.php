<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_id',
        'next_term_begins',
        'next_term_school_fees',
    ];

    protected $casts = [
        'next_term_begins' => 'date',
    ];

    /**
     * Get the school that owns these settings.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}