<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttendanceSubmission extends Model
{
    protected $fillable = [
        'uuid',
        'student_id',
        'class_id',
        'attendance_session_id',
        'class_qr_code_id',
        'submitted_at',
        'latitude',
        'longitude',
        'gps_accuracy_m',
        'gps_captured_at_client',
        'distance_from_school_m',
        'street_name',
        'address',
        'geofence_flag',
        'is_late',
        'status',
        'lock_version',
        'idempotency_key',
        'superseded_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'gps_captured_at_client' => 'datetime',
        'superseded_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy_m' => 'float',
        'distance_from_school_m' => 'float',
        'is_late' => 'boolean',
        'lock_version' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(ClassQrCode::class, 'class_qr_code_id');
    }

    public function photo(): HasOne
    {
        return $this->hasOne(AttendancePhoto::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(AttendanceValidation::class)->orderBy('decided_at', 'desc');
    }

    public function latestValidation(): HasOne
    {
        return $this->hasOne(AttendanceValidation::class)->latestOfMany('decided_at');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
