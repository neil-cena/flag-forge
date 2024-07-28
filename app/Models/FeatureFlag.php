<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'flag_key',
        'description',
        'is_enabled',
        'rollout_percentage',
        'variations',
        'metadata',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'variations' => 'array',
        'metadata' => 'array',
        'rollout_percentage' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function targetingRules(): HasMany
    {
        return $this->hasMany(TargetingRule::class)->orderBy('priority');
    }

    public function experiments(): HasMany
    {
        return $this->hasMany(Experiment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'flag_key';
    }
}
