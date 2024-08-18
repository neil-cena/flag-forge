<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'experiment_id',
        'user_identifier',
        'variant',
        'event_name',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }
}
