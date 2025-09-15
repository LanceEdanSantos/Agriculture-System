<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            SupplierSeeder::class, // Add before InventoryItemSeeder
            InventoryItemSeeder::class,
            // Keep ComprehensiveSeeder last as it may depend on other seeders
            ComprehensiveSeeder::class,
        ]);
    }
}
