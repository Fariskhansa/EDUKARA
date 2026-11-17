<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendancePhoto extends Model
{
    protected $fillable = [
        'attendance_submission_id',
        'disk',
        'path',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'sha256',
        'retention_until',
        'purged_at',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'retention_until' => 'date',
        'purged_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AttendanceSubmission::class, 'attendance_submission_id');
    }
}
