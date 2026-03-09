<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DirectPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permissions)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $requiredPermissions = collect(explode('|', $permissions))
            ->map(fn (string $permission) => trim($permission))
            ->filter()
            ->values();

        if ($requiredPermissions->isEmpty()) {
            return $next($request);
        }

        $isAllowed = $requiredPermissions->contains(
            fn (string $permission) => $user->hasDirectPermission($permission)
        );

        if (!$isAllowed) {
            abort(403);
        }

        return $next($request);
    }
}

