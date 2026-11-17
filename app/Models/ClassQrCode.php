<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassQrCode extends Model
{
    protected $fillable = [
        'class_id',
        'token',
        'is_active',
        'generated_by',
        'generated_at',
        'revoked_at',
        'revoked_by',
        'revoke_reason',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'generated_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AttendanceSubmission::class);
    }
}
