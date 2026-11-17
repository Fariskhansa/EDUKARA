<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    protected $fillable = [
        'attendance_period_id',
        'session_date',
        'opens_at',
        'closes_at',
        'late_after',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
        'late_after' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(AttendancePeriod::class, 'attendance_period_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AttendanceSubmission::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
