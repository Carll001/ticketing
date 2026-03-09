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
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]
        );

        $regularUser = User::firstOrCreate(
            ['email' => 'regularuser@gmail.com'],
            [
                'name' => 'regular user',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $superadmin->syncPermissions(Permission::pluck('name')->toArray());
        $regularUser->syncPermissions([]);
    }
}