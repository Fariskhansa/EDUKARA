<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'full_name',
        'gender',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function currentEnrollment(): ?ClassEnrollment
    {
        return $this->enrollments()
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->whereNull('left_at')
            ->latest('enrolled_at')
            ->first();
    }

    public function currentClass(): ?SchoolClass
    {
        return $this->currentEnrollment()?->schoolClass;
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')
            ->withPivot('relationship', 'is_primary_contact', 'verification_status', 'verified_at', 'notes')
            ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AttendanceSubmission::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }
}
