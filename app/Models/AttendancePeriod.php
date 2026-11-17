<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendancePeriod extends Model
{
    protected $fillable = [
        'name',
        'opens_at',
        'closes_at',
        'late_after',
        'active_days',
        'is_active',
    ];

    protected $casts = [
        'active_days' => 'array',
        'is_active' => 'boolean',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
