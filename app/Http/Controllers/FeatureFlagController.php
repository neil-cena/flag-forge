<?php

namespace App\Http\Controllers;

use App\Events\FeatureFlagUpdated;
use App\Models\FeatureFlag;
use App\Models\Project;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeatureFlagController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function show(Project $project, FeatureFlag $featureFlag): Response
    {
        abort_unless($featureFlag->project_id === $project->id, 404);

        $featureFlag->load(['targetingRules', 'experiments', 'project']);
        $auditLogs = $project->auditLogs()
            ->where('feature_flag_id', $featureFlag->id)
            ->latest()
            ->limit(30)
            ->get();

        return Inertia::render('Flags/Show', [
            'project' => $project,
            'featureFlag' => $featureFlag,
            'auditLogs' => $auditLogs,
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'flag_key' => ['required', 'alpha_dash', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_enabled' => ['required', 'boolean'],
            'rollout_percentage' => ['required', 'integer', 'between:0,100'],
            'variations' => ['nullable', 'array'],
            'targeting_rules' => ['nullable', 'array'],
        ]);

        $flag = $project->featureFlags()->create([
            'name' => $payload['name'],
            'flag_key' => $payload['flag_key'],
            'description' => $payload['description'] ?? null,
            'is_enabled' => $payload['is_enabled'],
            'rollout_percentage' => $payload['rollout_percentage'],
            'variations' => $payload['variations'] ?? ['on' => true, 'off' => false],
        ]);

        foreach ($payload['targeting_rules'] ?? [] as $index => $rule) {
            $flag->targetingRules()->create([
                'name' => (string) ($rule['name'] ?? 'Rule '.($index + 1)),
                'priority' => (int) ($rule['priority'] ?? (100 + $index)),
                'attribute' => (string) ($rule['attribute'] ?? 'country'),
                'operator' => (string) ($rule['operator'] ?? 'in'),
                'values' => $rule['values'] ?? [],
                'is_enabled' => (bool) ($rule['is_enabled'] ?? true),
            ]);
        }

        $flag->load('project');
        FeatureFlagUpdated::dispatch($flag);
        $this->auditLogService->log($project, 'flag.created', $flag, $request->user(), $payload, $request);

        return to_route('projects.index');
    }

    public function update(Request $request, Project $project, FeatureFlag $featureFlag): RedirectResponse
    {
        abort_unless($featureFlag->project_id === $project->id, 404);

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_enabled' => ['required', 'boolean'],
            'rollout_percentage' => ['required', 'integer', 'between:0,100'],
            'targeting_rules' => ['nullable', 'array'],
        ]);

        $featureFlag->update([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'is_enabled' => $payload['is_enabled'],
            'rollout_percentage' => $payload['rollout_percentage'],
        ]);

        $featureFlag->targetingRules()->delete();
        foreach ($payload['targeting_rules'] ?? [] as $index => $rule) {
            $featureFlag->targetingRules()->create([
                'name' => (string) ($rule['name'] ?? 'Rule '.($index + 1)),
                'priority' => (int) ($rule['priority'] ?? (100 + $index)),
                'attribute' => (string) ($rule['attribute'] ?? 'country'),
                'operator' => (string) ($rule['operator'] ?? 'in'),
                'values' => $rule['values'] ?? [],
                'is_enabled' => (bool) ($rule['is_enabled'] ?? true),
            ]);
        }

        $featureFlag->load('project');
        FeatureFlagUpdated::dispatch($featureFlag);
        $this->auditLogService->log($project, 'flag.updated', $featureFlag, $request->user(), $payload, $request);

        return to_route('flags.show', [$project, $featureFlag]);
    }
}
