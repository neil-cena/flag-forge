<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experiment;
use App\Models\ExperimentEvent;
use App\Services\ExperimentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExperimentEventController extends Controller
{
    public function __construct(private readonly ExperimentService $experimentService)
    {
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'experiment_id' => ['required', 'integer', 'exists:experiments,id'],
            'user_identifier' => ['required', 'string', 'max:255'],
            'event_name' => ['required', 'string', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ]);

        $experiment = Experiment::findOrFail($payload['experiment_id']);
        $variant = $this->experimentService->assignVariant($experiment, $payload['user_identifier']);

        $event = ExperimentEvent::create([
            'experiment_id' => $experiment->id,
            'user_identifier' => $payload['user_identifier'],
            'event_name' => $payload['event_name'],
            'variant' => $variant,
            'metadata' => $payload['metadata'] ?? null,
            'occurred_at' => now(),
        ]);

        return response()->json([
            'id' => $event->id,
            'variant' => $variant,
            'status' => 'tracked',
        ], 201);
    }
}
