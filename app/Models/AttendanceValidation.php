<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceValidation extends Model
{
    protected $fillable = [
        'attendance_submission_id',
        'validator_id',
        'action',
        'from_status',
        'to_status',
        'reason',
        'decided_at',
        'ip_address',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AttendanceSubmission::class, 'attendance_submission_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
}
