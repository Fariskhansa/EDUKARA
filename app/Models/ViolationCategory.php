<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'default_points',
        'severity',
        'is_active',
    ];

    protected $casts = [
        'default_points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function violations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }
}
