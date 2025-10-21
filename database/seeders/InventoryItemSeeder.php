<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

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

        $faker = Faker::create('en_PH'); // Use Philippine locale for more relevant data

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

        // Expanded item templates for variety - only using existing categories
        $itemTemplates = [
            // Seeds and planting materials
            ['category' => 'Assorted Vegetable Seeds', 'type' => 'seeds', 'prefix' => 'SEED'],
            ['category' => 'Organic Agriculture Development', 'type' => 'organic', 'prefix' => 'ORG'],
            ['category' => 'Seedlings and Plants', 'type' => 'seedlings', 'prefix' => 'TREE'],

            // Fertilizers and chemicals
            ['category' => 'Fertilizers', 'type' => 'fertilizer', 'prefix' => 'FERT'],
            ['category' => 'Pesticides', 'type' => 'pesticide', 'prefix' => 'PEST'],
            ['category' => 'Soil Amendments', 'type' => 'soil', 'prefix' => 'SOIL'],

            // Tools and equipment
            ['category' => 'Tools and Equipment', 'type' => 'tool', 'prefix' => 'TOOL'],
            ['category' => 'Irrigation Supplies', 'type' => 'irrigation', 'prefix' => 'IRRIG'],

            // Packaging and materials
            ['category' => 'Plastic Materials', 'type' => 'plastic', 'prefix' => 'PLAST'],
            ['category' => 'Other', 'type' => 'other', 'prefix' => 'MISC'],
        ];

        // Generate 500 items
        $createdCount = 0;
        $maxAttempts = 1000; // Prevent infinite loop
        $attempts = 0;

        while ($createdCount < 500 && $attempts < $maxAttempts) {
            $template = $faker->randomElement($itemTemplates);
            $attempts++;

            // Find category
            $category = $categories->where('name', $template['category'])->first();
            if (!$category) {
                continue; // Skip if category not found
            }

            // Select appropriate unit based on item type
            $unit = $this->getUnitForItemType($units, $template['type']);

            // Generate item details based on type
            $itemData = $this->generateItemData($faker, $template, $createdCount + 1);

            try {
                // Create the inventory item
                InventoryItem::create([
                    'name' => $itemData['name'],
                    'description' => $itemData['description'],
                    'category_id' => $category->id,
                    'unit_id' => $unit->id,
                    'unit_cost' => $itemData['unit_cost'],
                    'current_stock' => $itemData['current_stock'],
                    'minimum_stock' => $itemData['minimum_stock'],
                    'supplier_id' => $suppliers->random()->id,
                    'status' => $faker->randomElement(['active', 'active', 'active', 'inactive']), // Mostly active
                    'item_code' => $template['prefix'] . '-' . str_pad($createdCount + 1, 4, '0', STR_PAD_LEFT),
                    'average_unit_cost' => $itemData['unit_cost'],
                    'total_purchased' => $itemData['current_stock'],
                ]);

                $createdCount++;
            } catch (\Exception $e) {
                // If creation fails, continue to next item
                continue;
            }
        }

        if ($createdCount < 500) {
            $this->command->error("Only created {$createdCount} items out of 500 requested. Check category and unit data.");
        } else {
            $this->command->info("Successfully created {$createdCount} inventory items.");
        }
    }

    /**
     * Get appropriate unit for item type
     */
    private function getUnitForItemType($units, $type)
    {
        $unitMap = [
            'seeds' => ['packs/cans', 'kg', 'g'],
            'seedlings' => ['pcs', 'bundles'],
            'fertilizer' => ['sack', 'kg', 'l'],
            'pesticide' => ['l', 'ml', 'kg'],
            'soil' => ['sack', 'kg', 'l'],
            'tool' => ['pcs', 'set'],
            'irrigation' => ['pcs', 'm'],
            'plastic' => ['rolls', 'sheets', 'kg'],
            'organic' => ['sack', 'kg', 'l'],
            'other' => ['pcs', 'boxes', 'rolls'],
        ];

        $unitNames = $unitMap[$type] ?? ['pcs'];
        return $units->whereIn('name', $unitNames)->random() ?? $units->first();
    }

    /**
     * Generate item data based on type
     */
    private function generateItemData($faker, $template, $index)
    {
        $type = $template['type'];
        $baseNames = $this->getBaseNamesForType($type);

        $baseName = $faker->randomElement($baseNames);
        $variety = $faker->randomElement(['Premium', 'Standard', 'Organic', 'Hybrid', 'Regular', 'Professional']);

        $name = "{$variety} {$baseName}";

        // Generate descriptions based on type
        $descriptions = [
            'seeds' => [
                'High-quality seeds for optimal crop yield and disease resistance',
                'Premium grade seeds selected for maximum germination rates',
                'Carefully selected seeds for commercial farming operations',
            ],
            'fertilizer' => [
                'Balanced fertilizer for optimal plant nutrition and growth',
                'Slow-release formula for sustained plant nutrition',
                'Organic fertilizer promoting soil health and plant vitality',
            ],
            'pesticide' => [
                'Effective pest control solution for agricultural applications',
                'Targeted pest management for healthy crop production',
                'Environmentally friendly pest control option',
            ],
            'tool' => [
                'Professional-grade tool designed for agricultural work',
                'Durable and reliable equipment for farming operations',
                'High-quality tool built to last in demanding conditions',
            ],
            'organic' => [
                'Organic agricultural product promoting sustainable farming',
                'Natural and eco-friendly farming solution',
                'Certified organic product for healthy crop production',
            ],
            'soil' => [
                'Soil amendment for improved soil structure and fertility',
                'Organic soil conditioner for better plant growth',
                'Natural soil enhancer for sustainable agriculture',
            ],
            'irrigation' => [
                'Efficient irrigation solution for water conservation',
                'Professional irrigation equipment for modern farming',
                'High-quality irrigation supplies for optimal water distribution',
            ],
            'plastic' => [
                'Durable plastic material for agricultural applications',
                'High-quality plastic supplies for farming operations',
                'Professional-grade plastic materials for crop protection',
            ],
        ];

        $descriptionTemplates = $descriptions[$type] ?? ['High-quality agricultural product'];
        $description = $faker->randomElement($descriptionTemplates);

        // Generate realistic pricing and stock levels
        $pricing = $this->getPricingForType($type, $faker);
        $stock = $this->getStockForType($type, $faker);

        return [
            'name' => $name,
            'description' => $description,
            'unit_cost' => $pricing['unit_cost'],
            'current_stock' => $stock['current'],
            'minimum_stock' => $stock['minimum'],
        ];
    }

    /**
     * Get base names for different item types
     */
    private function getBaseNamesForType($type)
    {
        return [
            'seeds' => [
                'Tomato Seeds', 'Bell Pepper Seeds', 'Cabbage Seeds', 'Lettuce Seeds',
                'Carrot Seeds', 'Radish Seeds', 'Onion Seeds', 'Garlic Seeds',
                'Eggplant Seeds', 'Okra Seeds', 'Spinach Seeds', 'Broccoli Seeds'
            ],
            'fertilizer' => [
                'NPK Fertilizer', 'Organic Compost', 'Urea Fertilizer', 'Phosphate Fertilizer',
                'Potassium Fertilizer', 'Complete Fertilizer', 'Slow Release Fertilizer'
            ],
            'pesticide' => [
                'Insecticide', 'Neem Oil', 'Pyrethrin Spray', 'Bacillus Thuringiensis',
                'Organic Pest Control', 'Systemic Insecticide'
            ],
            'tool' => [
                'Garden Hoe', 'Hand Trowel', 'Pruning Shears', 'Garden Fork',
                'Watering Can', 'Sprayer', 'Wheelbarrow', 'Garden Gloves'
            ],
            'organic' => [
                'Organic Compost', 'Vermicompost', 'Bone Meal', 'Fish Emulsion',
                'Seaweed Extract', 'Organic Potting Mix', 'Composted Manure'
            ],
            'soil' => [
                'Perlite', 'Vermiculite', 'Coconut Coir', 'Peat Moss',
                'Garden Soil Mix', 'Potting Soil', 'Soil Conditioner'
            ],
            'irrigation' => [
                'Drip Irrigation Kit', 'Sprinkler System', 'Hose Pipe', 'Water Timer',
                'Sprinkler Head', 'Irrigation Valve', 'Water Pump'
            ],
            'plastic' => [
                'Mulch Film', 'Greenhouse Plastic', 'Drip Tubing',
                'Plant Pots', 'Seedling Trays', 'Storage Bags'
            ],
            'seedlings' => [
                'Tomato Seedlings', 'Pepper Plants', 'Cabbage Starts', 'Lettuce Plants',
                'Herb Seedlings', 'Flower Plants', 'Fruit Tree Saplings'
            ],
        ][$type] ?? ['Generic Item'];
    }

    /**
     * Get realistic pricing for different item types
     */
    private function getPricingForType($type, $faker)
    {
        $priceRanges = [
            'seeds' => [50, 300],      // ₱50-300 per pack
            'fertilizer' => [200, 1500], // ₱200-1500 per sack/kg
            'pesticide' => [150, 800],   // ₱150-800 per liter/kg
            'tool' => [100, 2500],      // ₱100-2500 per piece
            'plastic' => [80, 2000],    // ₱80-2000 per roll/piece
            'organic' => [150, 1200],   // ₱150-1200 per sack/kg
            'soil' => [100, 800],       // ₱100-800 per sack/kg
            'irrigation' => [200, 3000], // ₱200-3000 per piece
            'seedlings' => [25, 150],   // ₱25-150 per plant
        ];

        $range = $priceRanges[$type] ?? [100, 500];
        return [
            'unit_cost' => $faker->randomFloat(2, $range[0], $range[1])
        ];
    }

    /**
     * Get realistic stock levels for different item types
     */
    private function getStockForType($type, $faker)
    {
        $stockRanges = [
            'seeds' => [10, 100],      // 10-100 packs
            'fertilizer' => [5, 50],   // 5-50 sacks
            'pesticide' => [8, 40],    // 8-40 liters
            'tool' => [3, 25],         // 3-25 pieces
            'plastic' => [15, 80],     // 15-80 rolls
            'organic' => [8, 45],      // 8-45 sacks
            'soil' => [12, 60],        // 12-60 sacks
            'irrigation' => [5, 30],   // 5-30 pieces
            'seedlings' => [20, 150],  // 20-150 plants
        ];

        $range = $stockRanges[$type] ?? [5, 30];
        $current = $faker->numberBetween($range[0], $range[1]);
        $minimum = $faker->numberBetween(1, max(2, intval($current * 0.2)));

        return [
            'current' => $current,
            'minimum' => $minimum,
        ];
    }
}
