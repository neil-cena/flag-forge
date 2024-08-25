<?php

namespace Database\Seeders;

use App\Models\Experiment;
use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\SdkKey;
use App\Models\TargetingRule;
use Illuminate\Database\Seeder;

class FlagForgeSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::firstOrCreate(
            ['project_key' => 'flagforge-demo'],
            [
                'name' => 'FlagForge Demo',
                'description' => 'Seeded project for local review.',
                'is_active' => true,
            ]
        );

        $flag = FeatureFlag::firstOrCreate(
            ['project_id' => $project->id, 'flag_key' => 'new_checkout_flow'],
            [
                'name' => 'New Checkout Flow',
                'description' => 'Enable redesigned checkout.',
                'is_enabled' => true,
                'rollout_percentage' => 20,
                'variations' => ['on' => true, 'off' => false],
            ]
        );

        TargetingRule::firstOrCreate(
            ['feature_flag_id' => $flag->id, 'name' => 'PH users'],
            [
                'priority' => 10,
                'attribute' => 'country',
                'operator' => 'in',
                'values' => ['PH'],
                'is_enabled' => true,
            ]
        );

        TargetingRule::firstOrCreate(
            ['feature_flag_id' => $flag->id, 'name' => 'Beta testers'],
            [
                'priority' => 20,
                'attribute' => 'segments',
                'operator' => 'contains',
                'values' => ['beta'],
                'is_enabled' => true,
            ]
        );

        Experiment::firstOrCreate(
            ['feature_flag_id' => $flag->id, 'name' => 'Checkout CTA Color Test'],
            [
                'status' => 'running',
                'variants' => ['control', 'variant_a'],
                'allocation' => ['control' => 50, 'variant_a' => 50],
                'hypothesis' => 'Variant A improves checkout completion.',
                'started_at' => now()->subDays(14),
            ]
        );

        SdkKey::firstOrCreate(
            ['project_id' => $project->id, 'name' => 'Local SDK Key'],
            [
                'key_hash' => hash('sha256', 'ff_demo_local_key'),
                'scopes' => ['evaluate', 'track'],
                'is_active' => true,
            ]
        );
    }
}
