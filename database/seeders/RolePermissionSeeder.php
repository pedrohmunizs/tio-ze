<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'providers.view', 'providers.create', 'providers.edit', 'providers.delete', 'providers.approve',
            'drivers.view', 'drivers.create', 'drivers.edit', 'drivers.delete',
            'children.view', 'children.create', 'children.edit', 'children.delete',
            'vehicles.view', 'vehicles.create', 'vehicles.edit', 'vehicles.delete',
            'routes.view', 'routes.create', 'routes.edit', 'routes.delete',
            'trips.view', 'trips.create', 'trips.edit', 'trips.delete', 'trips.start', 'trips.complete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $provider = Role::firstOrCreate(['name' => 'provider']);
        $driver = Role::firstOrCreate(['name' => 'driver']);
        $parent = Role::firstOrCreate(['name' => 'parent']);

        // $admin->syncPermissions(Permission::all());
        
        $parent->syncPermissions([
            'children.view', 'children.create', 'children.edit', 'children.delete',
        ]);
    }
}
