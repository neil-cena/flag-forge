<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function index(): Response
    {
        $projects = Project::withCount('featureFlags')
            ->with(['featureFlags' => function ($query) {
                $query->latest()->limit(20);
            }])
            ->orderBy('name')
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'project_key' => ['required', 'alpha_dash', 'max:50', 'unique:projects,project_key'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $project = Project::create($payload);
        $this->auditLogService->log($project, 'project.created', null, $request->user(), $payload, $request);

        return to_route('projects.index');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ]);

        $project->update($payload);
        $this->auditLogService->log($project, 'project.updated', null, $request->user(), $payload, $request);

        return to_route('projects.index');
    }
}
