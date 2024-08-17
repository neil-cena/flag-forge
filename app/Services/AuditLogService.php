<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogService
{
    public function log(
        Project $project,
        string $action,
        ?FeatureFlag $flag = null,
        ?User $user = null,
        ?array $payload = null,
        ?Request $request = null
    ): AuditLog {
        return AuditLog::create([
            'project_id' => $project->id,
            'feature_flag_id' => $flag?->id,
            'user_id' => $user?->id,
            'action' => $action,
            'payload' => $payload,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
