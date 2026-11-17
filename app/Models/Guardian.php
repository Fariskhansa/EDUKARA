<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
            ->withPivot('id', 'relationship', 'is_primary_contact', 'verification_status', 'verified_at', 'notes')
            ->withTimestamps();
    }

    public function verifiedStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('verification_status', 'verified');
    }

    public function studentLinks(): HasMany
    {
        return $this->hasMany(GuardianStudent::class, 'guardian_id');
    }
}
