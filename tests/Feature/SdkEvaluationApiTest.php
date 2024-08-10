<?php

namespace Tests\Feature;

use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\SdkKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SdkEvaluationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_evaluates_flags_via_sdk_api(): void
    {
        $project = Project::create([
            'name' => 'Demo',
            'project_key' => 'demo',
            'is_active' => true,
        ]);

        FeatureFlag::create([
            'project_id' => $project->id,
            'name' => 'New onboarding',
            'flag_key' => 'new_onboarding',
            'is_enabled' => true,
            'rollout_percentage' => 100,
            'variations' => ['on' => true, 'off' => false],
        ]);

        $token = 'sdk_token_demo';
        SdkKey::create([
            'project_id' => $project->id,
            'name' => 'Local',
            'key_hash' => hash('sha256', $token),
            'is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/v1/evaluate', [
            'context' => [
                'user_identifier' => 'user-1',
                'country' => 'US',
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('project_key', 'demo')
            ->assertJsonPath('flags.new_onboarding.enabled', true);
    }
}
