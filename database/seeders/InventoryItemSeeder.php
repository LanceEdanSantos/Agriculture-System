<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if no inventory items exist
        if (InventoryItem::count() > 0) {
            return;
        }

        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        
        // Ensure we have at least one supplier
        if ($suppliers->isEmpty()) {
            $supplier = Supplier::create([
                'name' => 'Default Supplier',
                'company_name' => 'Default Supplier Co.',
                'contact_persons' => json_encode([
                    ['name' => 'Main Contact', 'email' => 'contact@defaultsupplier.com', 'phone' => '+1 (555) 000-0000']
                ]),
                'phone_numbers' => json_encode(['+1 (555) 000-0000']),
                'email_addresses' => json_encode(['contact@defaultsupplier.com']),
                'address' => '123 Supplier St, City, Country',
                'website' => 'https://defaultsupplier.com',
                'tax_id' => 'TIN-000000000',
                'business_license' => 'BL-2024-000',
                'notes' => 'Default supplier',
                'status' => 'active',
            ]);
            $suppliers = collect([$supplier]);
        }

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
                'item_code' => 'VEG-TOMATO-001',
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
                'item_code' => 'FERT-ORG-001',
            ],
            [
                'name' => 'Garden Shovel - Heavy Duty',
                'description' => 'Heavy-duty garden shovel for professional use',
                'category_id' => $categories->where('name', 'Tools and Equipment')->first()->id,
                'unit_id' => $units->where('name', 'pcs')->first()->id,
                'unit_cost' => 450.00,
                'current_stock' => 15,
                'minimum_stock' => 3,
                'supplier_id' => $suppliers->get(2)->id ?? $suppliers->first()->id,
                'status' => 'active',
                'item_code' => 'TOOL-SHOVEL-001',
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
                'item_code' => 'PLAST-MULCH-001',
            ],
            [
                'name' => 'Neem Oil - Natural Pesticide',
                'description' => 'Natural neem oil for organic pest control',
                'category_id' => $categories->where('name', 'Pesticides')->first()->id,
                'unit_id' => $units->where('name', 'l')->first()->id,
                'unit_cost' => 350.00,
                'current_stock' => 30,
                'minimum_stock' => 5,
                'supplier_id' => $suppliers->get(1)->id ?? $suppliers->first()->id,
                'status' => 'active',
                'item_code' => 'PEST-NEEM-001',
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create($item);
        }
    }
}
