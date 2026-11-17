<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    protected $table = 'academic_calendar';

    protected $fillable = [
        'date',
        'type',
        'description',
        'is_school_day',
    ];

    protected $casts = [
        'date' => 'date',
        'is_school_day' => 'boolean',
    ];
}
