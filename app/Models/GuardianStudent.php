<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardianStudent extends Model
{
    protected $table = 'guardian_student';

    protected $fillable = [
        'guardian_id',
        'student_id',
        'relationship',
        'is_primary_contact',
        'verification_status',
        'verified_by',
        'verified_at',
        'revoked_at',
        'notes',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
        'verified_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
