<?php

namespace App\Http\Middleware;

use App\Models\SdkKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SdkAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) preg_replace('/^Bearer\s+/i', '', (string) $request->header('Authorization', ''));
        if ($token === '') {
            return response()->json(['message' => 'Missing SDK token.'], 401);
        }

        $keyHash = hash('sha256', $token);
        $sdkKey = SdkKey::with('project')
            ->where('key_hash', $keyHash)
            ->where('is_active', true)
            ->first();

        if (! $sdkKey || ! $sdkKey->project || ! $sdkKey->project->is_active) {
            return response()->json(['message' => 'Invalid SDK token.'], 401);
        }

        $sdkKey->forceFill(['last_used_at' => now()])->save();
        $request->attributes->set('sdkKey', $sdkKey);
        $request->attributes->set('project', $sdkKey->project);

        return $next($request);
    }
}
