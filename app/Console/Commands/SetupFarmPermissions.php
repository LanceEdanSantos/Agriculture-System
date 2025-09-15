<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SetupFarmPermissions extends Command
{
    protected $signature = 'permission:setup-farm';
    protected $description = 'Set up permissions for Farm resource';

    public function handle()
    {
        $this->info('Setting up Farm permissions...');

        // Create permissions
        $permissions = [
            'view_any_farm',
            'view_farm',
            'create_farm',
            'update_farm',
            'delete_farm',
            'restore_farm',
            'force_delete_farm',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to super_admin role
        if ($superAdminRole = Role::where('name', 'super_admin')->first()) {
            $superAdminRole->givePermissionTo($permissions);
            $this->info('Assigned all permissions to super_admin role');
        }

        // Assign view and update permissions to doa_staff role
        if ($staffRole = Role::where('name', 'doa_staff')->first()) {
            $staffPermissions = ['view_any_farm', 'view_farm', 'update_farm'];
            $staffRole->givePermissionTo($staffPermissions);
            $this->info('Assigned view and update permissions to doa_staff role');
        }

        $this->info('Farm permissions set up successfully!');
        return Command::SUCCESS;
    }
}
