<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage-classes',
            'view-classes',
            'manage-sections',
            'view-sections',
            'manage-users',
            'view-users',
            'manage-timetables',
            'view-timetables',
            'manage-devices',
            'view-devices',
            'manage-holidays',
            'view-holidays',
            'manage-leaves',
            'view-leaves',
            'manage-attendance',
            'view-attendance',
            'view-reports',
            'manage-roles-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $roles = [
            'admin' => $permissions,
            'teacher' => ['view-attendance', 'view-reports', 'view-classes', 'view-sections'],
            'student' => [],
            'operator' => ['manage-attendance', 'view-users', 'manage-users', 'view-attendance'],
            'accountant' => ['view-users', 'view-reports', 'view-leaves', 'manage-leaves', 'view-holidays'],
            'office_boy' => [],
            'staff' => [],
        ];
        
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Sync existing users to Spatie Roles based on their 'role' column
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role) {
                if (Role::where('name', $user->role)->exists()) {
                    $user->assignRole($user->role);
                }
            }
        }
    }
}
