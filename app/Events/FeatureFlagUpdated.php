<?php

namespace App\Events;

use App\Models\FeatureFlag;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeatureFlagUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public FeatureFlag $featureFlag)
    {}

    public function broadcastOn(): array
    {
        return [
            new Channel('projects.'.$this->featureFlag->project->project_key.'.flags'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'flag.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->featureFlag->id,
            'flag_key' => $this->featureFlag->flag_key,
            'is_enabled' => $this->featureFlag->is_enabled,
            'rollout_percentage' => $this->featureFlag->rollout_percentage,
        ];
    }
}
