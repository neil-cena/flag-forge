<?php

namespace Tests\Feature;

use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\TargetingRule;
use App\Services\FlagEvaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureFlagEvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_evaluates_targeting_rules_before_rollout(): void
    {
        $project = Project::create([
            'name' => 'Demo',
            'project_key' => 'demo',
            'is_active' => true,
        ]);

        $flag = FeatureFlag::create([
            'project_id' => $project->id,
            'name' => 'Checkout',
            'flag_key' => 'checkout',
            'is_enabled' => true,
            'rollout_percentage' => 0,
            'variations' => ['on' => true, 'off' => false],
        ]);

        TargetingRule::create([
            'feature_flag_id' => $flag->id,
            'name' => 'PH rule',
            'priority' => 10,
            'attribute' => 'country',
            'operator' => 'in',
            'values' => ['PH'],
            'is_enabled' => true,
        ]);

        $result = app(FlagEvaluationService::class)->evaluate($project, [
            'country' => 'PH',
            'user_identifier' => 'user-123',
        ]);

        $this->assertTrue($result['checkout']['enabled']);
        $this->assertSame('rule:PH rule', $result['checkout']['reason']);
    }
}
