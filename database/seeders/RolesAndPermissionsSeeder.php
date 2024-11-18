<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        Permission::create(['name' => 'edit items']);
        Permission::create(['name' => 'delete items']);
        Permission::create(['name' => 'view items']);
        Permission::create(['name' => 'create items']);

        // Create roles and assign existing permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(['edit items', 'delete items', 'view items', 'create items']);

        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo(['view items']);
    }
}
