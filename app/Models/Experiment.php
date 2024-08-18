<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experiment extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature_flag_id',
        'name',
        'status',
        'variants',
        'allocation',
        'hypothesis',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'variants' => 'array',
        'allocation' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function featureFlag(): BelongsTo
    {
        return $this->belongsTo(FeatureFlag::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ExperimentEvent::class);
    }
}
