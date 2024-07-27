<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $projects = Project::withCount('featureFlags')
            ->orderBy('name')
            ->get(['id', 'name', 'project_key', 'is_active']);

        $recentAudits = [];
        if ($projects->isNotEmpty()) {
            $recentAudits = $projects->first()
                ->auditLogs()
                ->latest()
                ->limit(10)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'projects' => $projects,
            'recentAudits' => $recentAudits,
            'viewer' => Auth::user()?->only(['id', 'name', 'email']),
        ]);
    }
}
