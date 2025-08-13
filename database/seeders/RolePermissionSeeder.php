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
        // Create permissions for all resources
        $permissions = [
            // Inventory Item permissions
            'view_inventory::item',
            'view_any_inventory::item',
            'create_inventory::item',
            'update_inventory::item',
            'delete_inventory::item',
            'delete_any_inventory::item',

            // Purchase Request permissions
            'view_purchase::request',
            'view_any_purchase::request',
            'create_purchase::request',
            'update_purchase::request',
            'delete_purchase::request',
            'delete_any_purchase::request',

            // User permissions
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles
        $administratorRole = Role::findOrCreate('super_admin');
        $doaStaffRole = Role::findOrCreate('doa_staff');
        $farmerRole = Role::findOrCreate('farmer');

        // Assign permissions to roles
        $administratorRole->givePermissionTo(Permission::all());

        $doaStaffRole->givePermissionTo([
            'view_inventory::item',
            'view_any_inventory::item',
            'create_inventory::item',
            'update_inventory::item',
            'view_purchase::request',
            'view_any_purchase::request',
            'create_purchase::request',
            'update_purchase::request',
            'view_user',
            'view_any_user',
        ]);

        $farmerRole->givePermissionTo([
            'view_inventory::item',
            'view_any_inventory::item',
            'view_purchase::request',
            'view_any_purchase::request',
        ]);

        // Create default administrator user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@doa.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'administrator',
            'department' => 'IT Department',
            'position' => 'System Administrator',
        ]);

        $admin->assignRole('super_admin');

        // Create default DOA staff user
        $doaStaff = User::create([
            'name' => 'DOA Staff',
            'email' => 'staff@doa.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'doa_staff',
            'department' => 'Office of the Provincial Agriculturist',
            'position' => 'Staff',
        ]);

        $doaStaff->assignRole('doa_staff');

        // Create default farmer user
        $farmer = User::create([
            'name' => 'Sample Farmer',
            'email' => 'farmer@example.com',
            'password' => bcrypt('password'),
            'role' => 'farmer',
            'department' => 'N/A',
            'position' => 'Farmer',
        ]);

        $farmer->assignRole('farmer');
    }
}
