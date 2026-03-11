<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class ManageUserService
{
    public function store(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'staff';

        $departmentIds = $data['department_ids'] ?? [];
        $permissions = $data['permissions'] ?? [];
        unset($data['department_ids'], $data['permissions']);

        $user = User::create($data);
        $user->departments()->sync($departmentIds);
        $user->syncPermissions($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }

    public function update(User $user, array $data): User
    {
        if (!empty($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $departmentIds = $data['department_ids'] ?? null;
        $permissions = $data['permissions'] ?? null;
        unset($data['department_ids'], $data['permissions']);

        $user->update($data);

        if ($departmentIds !== null) {
            $user->departments()->sync($departmentIds);
        }

        if ($permissions !== null) {
            $user->syncPermissions($permissions);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        return $user;
    }

    public function destroy(User $user): void
    {
        $user->delete();
    }
}
