<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class ManageUserService
{
    public function store(array $data): User
    {
        $user = DB::transaction(function () use ($data): User {
            $data['password'] = Hash::make($data['password']);
            $data['role'] = $data['role'] ?? 'staff';

            $departmentIds = $data['department_ids'] ?? [];
            $permissions = $this->resolvePermissions($data['permissions'] ?? []);
            unset($data['department_ids'], $data['permissions']);

            $user = User::create($data);
            $user->departments()->sync($departmentIds);
            $user->syncRoles([]);
            $user->syncPermissions($permissions);

            return $user->refresh();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $updatedUser = DB::transaction(function () use ($user, $data): User {
            if (!empty($data['password'] ?? null)) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $departmentIds = $data['department_ids'] ?? null;
            $permissions = $this->resolvePermissions($data['permissions'] ?? []);
            unset($data['department_ids'], $data['permissions']);

            $user->update($data);

            if ($departmentIds !== null) {
                $user->departments()->sync($departmentIds);
            }

            $user->syncRoles([]);
            $user->syncPermissions($permissions);

            return $user->refresh();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $updatedUser;
    }

    public function destroy(User $user): void
    {
        $user->delete();
    }

    private function resolvePermissions(array $permissionNames): array
    {
        if (empty($permissionNames)) {
            return [];
        }

        return Permission::query()
            ->where('guard_name', config('auth.defaults.guard', 'web'))
            ->whereIn('name', $permissionNames)
            ->pluck('name')
            ->values()
            ->all();
    }
}
