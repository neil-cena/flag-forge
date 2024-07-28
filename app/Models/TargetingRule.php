<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature_flag_id',
        'name',
        'priority',
        'attribute',
        'operator',
        'values',
        'is_enabled',
    ];

    protected $casts = [
        'values' => 'array',
        'is_enabled' => 'boolean',
        'priority' => 'integer',
    ];

    public function featureFlag(): BelongsTo
    {
        return $this->belongsTo(FeatureFlag::class);
    }
}
