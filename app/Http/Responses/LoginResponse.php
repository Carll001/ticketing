<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->to(config('fortify.home'));
        }

        if ($user->isSuperAdmin()) {
            return redirect()->to(route('dashboard'));
        }

        $landingRoutes = [
            'manage dashboard' => route('dashboard'),
            'manage tasks' => route('task.index'),
            'manage users' => route('user.index'),
            'manage departments' => route('department.index'),
            'manage task presets' => route('taskPreset.index'),
            'manage transactions' => route('transaction.index'),
        ];

        foreach ($landingRoutes as $permission => $route) {
            if ($user->hasDirectPermission($permission)) {
                return redirect()->to($route);
            }
        }

        return redirect()->to(route('profile.edit'));
    }
}
