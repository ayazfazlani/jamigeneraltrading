<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Global Permissions
        Permission::create(['name' => 'manage super admin']);
        Permission::create(['name' => 'manage team']);

        // Item-related Permissions
        Permission::create(['name' => 'view items']);
        Permission::create(['name' => 'edit items']);
        Permission::create(['name' => 'create items']);
        Permission::create(['name' => 'delete items']);

        // Stock-related Permissions
        Permission::create(['name' => 'view stock']);
        Permission::create(['name' => 'manage stock']);

        // Analytics Permissions
        Permission::create(['name' => 'view analytics']);
        Permission::create(['name' => 'manage analytics']);

        // Create Global Roles
        $superAdmin = Role::create(['name' => 'super admin']);
        $superAdmin->givePermissionTo([
            'manage super admin',
            'manage team',
            'view items',
            'edit items',
            'create items',
            'delete items',
            'view stock',
            'manage stock',
            'view analytics',
            'manage analytics'
        ]);

        // Team-specific Roles
        $teamAdmin = Role::create(['name' => 'team admin']);
        $teamAdmin->givePermissionTo([
            'manage team',
            'view items',
            'edit items',
            'create items',
            'view stock',
            'view analytics'
        ]);

        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo([
            'view items',
            'edit items',
            'view stock'
        ]);

        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view items',
            'view stock'
        ]);
    }
}
