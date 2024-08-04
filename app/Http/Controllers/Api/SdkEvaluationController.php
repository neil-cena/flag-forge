<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\FlagEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SdkEvaluationController extends Controller
{
    public function __construct(private readonly FlagEvaluationService $flagEvaluationService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var Project $project */
        $project = $request->attributes->get('project');

        $payload = $request->validate([
            'context' => ['nullable', 'array'],
        ]);

        $cacheKey = 'sdk-eval:'.$project->project_key.':'.md5(json_encode($payload['context'] ?? []));
        $evaluations = Cache::remember($cacheKey, 10, function () use ($project, $payload) {
            return $this->flagEvaluationService->evaluate($project, $payload['context'] ?? []);
        });

        return response()->json([
            'project_key' => $project->project_key,
            'flags' => $evaluations,
            'evaluated_at' => now()->toIso8601String(),
        ]);
    }
}
