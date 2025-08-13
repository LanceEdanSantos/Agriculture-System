<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\User;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Hash;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Categories (only if they don't exist)
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }
        
        // Seed Units (only if they don't exist)
        if (Unit::count() === 0) {
            $this->call(UnitSeeder::class);
        }
        
        // Seed Sample Suppliers (only if they don't exist)
        if (Supplier::count() === 0) {
            $this->seedSuppliers();
        }
        
        // Seed Sample Inventory Items (only if they don't exist)
        if (InventoryItem::count() === 0) {
            $this->seedInventoryItems();
        }
        
        // Seed Sample Users (if not already seeded)
        $this->seedUsers();
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            [
                'name' => 'ABC Agricultural Supplies',
                'company_name' => 'ABC Agricultural Supplies Co.',
                'contact_persons' => ['John Smith', 'Maria Garcia'],
                'phone_numbers' => ['+63 912 345 6789', '+63 923 456 7890'],
                'email_addresses' => ['john@abcagri.com', 'maria@abcagri.com'],
                'address' => '123 Agriculture Street, Quezon City, Metro Manila',
                'website' => 'https://abcagri.com',
                'tax_id' => 'TIN-123456789',
                'business_license' => 'BL-2024-001',
                'notes' => 'Reliable supplier of seeds and fertilizers',
                'status' => 'active',
            ],
            [
                'name' => 'Green Thumb Seeds',
                'company_name' => 'Green Thumb Seeds Corporation',
                'contact_persons' => ['Pedro Santos'],
                'phone_numbers' => ['+63 934 567 8901'],
                'email_addresses' => ['pedro@greenthumb.com'],
                'address' => '456 Seed Avenue, Makati City, Metro Manila',
                'website' => 'https://greenthumb.com',
                'tax_id' => 'TIN-987654321',
                'business_license' => 'BL-2024-002',
                'notes' => 'Specialized in high-quality vegetable seeds',
                'status' => 'active',
            ],
            [
                'name' => 'Farm Tools Pro',
                'company_name' => 'Farm Tools Professional Equipment',
                'contact_persons' => ['Ana Rodriguez', 'Carlos Lopez'],
                'phone_numbers' => ['+63 945 678 9012', '+63 956 789 0123'],
                'email_addresses' => ['ana@farmtools.com', 'carlos@farmtools.com'],
                'address' => '789 Tool Road, Pasig City, Metro Manila',
                'website' => 'https://farmtools.com',
                'tax_id' => 'TIN-456789123',
                'business_license' => 'BL-2024-003',
                'notes' => 'Professional farming equipment and tools',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }

    private function seedInventoryItems(): void
    {
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();

        $items = [
            [
                'name' => 'Tomato Seeds - Hybrid',
                'description' => 'High-yield hybrid tomato seeds for commercial farming',
                'category_id' => $categories->where('name', 'Assorted Vegetable Seeds')->first()->id,
                'unit_id' => $units->where('name', 'packs/cans')->first()->id,
                'unit_cost' => 150.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'supplier_id' => $suppliers->first()->id,
                'status' => 'active',
            ],
            [
                'name' => 'Organic Fertilizer - NPK',
                'description' => 'Balanced NPK organic fertilizer for all crops',
                'category_id' => $categories->where('name', 'Fertilizers')->first()->id,
                'unit_id' => $units->where('name', 'sack')->first()->id,
                'unit_cost' => 850.00,
                'current_stock' => 25,
                'minimum_stock' => 5,
                'supplier_id' => $suppliers->first()->id,
                'status' => 'active',
            ],
            [
                'name' => 'Garden Shovel - Heavy Duty',
                'description' => 'Heavy-duty garden shovel for professional use',
                'category_id' => $categories->where('name', 'Tools and Equipment')->first()->id,
                'unit_id' => $units->where('name', 'pcs')->first()->id,
                'unit_cost' => 450.00,
                'current_stock' => 15,
                'minimum_stock' => 3,
                'supplier_id' => $suppliers->get(2)->id,
                'status' => 'active',
            ],
            [
                'name' => 'Plastic Mulch - Black',
                'description' => 'Black plastic mulch for weed control and soil warming',
                'category_id' => $categories->where('name', 'Plastic Materials')->first()->id,
                'unit_id' => $units->where('name', 'rolls')->first()->id,
                'unit_cost' => 1200.00,
                'current_stock' => 8,
                'minimum_stock' => 2,
                'supplier_id' => $suppliers->first()->id,
                'status' => 'active',
            ],
            [
                'name' => 'Neem Oil - Natural Pesticide',
                'description' => 'Natural neem oil for organic pest control',
                'category_id' => $categories->where('name', 'Pesticides')->first()->id,
                'unit_id' => $units->where('name', 'l')->first()->id,
                'unit_cost' => 350.00,
                'current_stock' => 30,
                'minimum_stock' => 5,
                'supplier_id' => $suppliers->get(1)->id,
                'status' => 'active',
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create($item);
        }
    }

    private function seedUsers(): void
    {
        // Only create users if they don't exist
        if (User::count() === 0) {
            $users = [
                [
                    'name' => 'System Administrator',
                    'email' => 'admin@doa.gov.ph',
                    'password' => Hash::make('password'),
                    'role' => 'administrator',
                    'department' => 'IT Department',
                    'position' => 'System Administrator',
                    'phone' => '+63 912 345 6789',
                    'address' => 'Department of Agriculture, Quezon City',
                ],
                [
                    'name' => 'Juan Dela Cruz',
                    'email' => 'juan.delacruz@doa.gov.ph',
                    'password' => Hash::make('password'),
                    'role' => 'doa_staff',
                    'department' => 'Procurement Division',
                    'position' => 'Procurement Officer',
                    'phone' => '+63 923 456 7890',
                    'address' => 'Department of Agriculture, Quezon City',
                ],
                [
                    'name' => 'Maria Santos',
                    'email' => 'maria.santos@doa.gov.ph',
                    'password' => Hash::make('password'),
                    'role' => 'doa_staff',
                    'department' => 'Inventory Management',
                    'position' => 'Inventory Specialist',
                    'phone' => '+63 934 567 8901',
                    'address' => 'Department of Agriculture, Quezon City',
                ],
            ];

            foreach ($users as $user) {
                User::create($user);
            }
        }
    }
} 
