<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage dashboard',
            'manage users',
            'manage departments',
            'manage tasks',
            'manage task presets',
            'manage transactions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => config('auth.defaults.guard', 'web'),
            ]);
        }

        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $regularUser = User::firstOrCreate(
            ['email' => 'regularuser@gmail.com'],
            [
                'name' => 'regular staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        // Super admin bypasses permission checks via Gate::before.
        $superadmin->syncRoles([]);
        $superadmin->syncPermissions([]);
        $admin->syncRoles([]);
        $admin->syncPermissions([]);
        $regularUser->syncRoles([]);
        $regularUser->syncPermissions([]);
    }
}
