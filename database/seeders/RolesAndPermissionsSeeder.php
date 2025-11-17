<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for resources
        $permissions = [
            // ItemRequest permissions
            'view_any_item_request',
            'view_item_request',
            'create_item_request',
            'update_item_request',
            'delete_item_request',
            'delete_any_item_request',
            'force_delete_item_request',
            'force_delete_any_item_request',
            'restore_item_request',
            'reorder_item_request',

            // Item permissions
            'view_any_item',
            'view_item',
            'create_item',
            'update_item',
            'delete_item',
            'delete_any_item',
            'force_delete_item',
            'force_delete_any_item',
            'restore_item',
            'reorder_item',

            // Farm permissions
            'view_any_farm',
            'view_farm',
            'create_farm',
            'update_farm',
            'delete_farm',
            'delete_any_farm',
            'force_delete_farm',
            'force_delete_any_farm',
            'restore_farm',
            'reorder_farm',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles and assign permissions
        $this->createAdminRole();
        $this->createFarmerRole();
    }

    protected function createAdminRole(): void
    {
        $role = Role::findOrCreate('Administrator');
        $role->givePermissionTo(Permission::all());

        // Assign to first user if exists
        $user = User::updateOrCreate([
            'email' => 'admin@doa.gov.ph',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        if ($user) {
            $user->assignRole($role);
        }
    }

    protected function createFarmerRole(): void
    {
        $role = Role::findOrCreate('Farmer');

        // Basic permissions for farmers
        $farmerPermissions = [
            // ItemRequest permissions
            'view_any_item_request',
            'view_item_request',
            'create_item_request',
            'update_item_request',
            'delete_item_request',

            // Item permissions (view only)
            'view_any_item',
            'view_item',

            // Farm permissions (view only)
            'view_any_farm',
            'view_farm',
        ];

        $role->syncPermissions($farmerPermissions);

        $user = User::updateOrCreate([
            'email' => 'farmer@example.com',
        ], [
            'name' => 'Farmer User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($role);
    }
}
