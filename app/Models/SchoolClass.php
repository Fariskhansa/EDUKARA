<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'academic_year_id',
        'grade_level',
        'name',
        'program',
        'homeroom_teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }

    public function activeQrCode(): HasOne
    {
        return $this->hasOne(ClassQrCode::class, 'class_id')->where('is_active', true);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(ClassQrCode::class, 'class_id');
    }

    public function officers(): HasMany
    {
        return $this->hasMany(ClassOfficer::class, 'class_id');
    }
}
