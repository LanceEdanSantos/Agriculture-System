<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Category;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $toolsAndEquipmentCategory = Category::where('slug', 'tools-and-equipment')->first();
        $irrigationCategory = Category::where('slug', 'irrigation-supplies')->first();
        $otherCategory = Category::where('slug', 'other')->first();

        // Default to "Other" category if specific categories don't exist
        $defaultCategory = $otherCategory ?? Category::first();

        $units = [
            // Count units - Tools and Equipment
            ['name' => 'packs/cans', 'display_name' => 'Packs/Cans', 'abbreviation' => 'packs', 'description' => 'Packs or cans', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],
            ['name' => 'pcs', 'display_name' => 'Pieces', 'abbreviation' => 'pcs', 'description' => 'Pieces', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],
            ['name' => 'boxes', 'display_name' => 'Boxes', 'abbreviation' => 'boxes', 'description' => 'Boxes', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],
            ['name' => 'bundles', 'display_name' => 'Bundles', 'abbreviation' => 'bundles', 'description' => 'Bundles', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],
            ['name' => 'sets', 'display_name' => 'Sets', 'abbreviation' => 'sets', 'description' => 'Sets', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],

            // Weight units - Other
            ['name' => 'kg', 'display_name' => 'Kilograms', 'abbreviation' => 'kg', 'description' => 'Kilograms', 'category_id' => $defaultCategory->id],
            ['name' => 'g', 'display_name' => 'Grams', 'abbreviation' => 'g', 'description' => 'Grams', 'category_id' => $defaultCategory->id],
            ['name' => 'sack', 'display_name' => 'Sack (50kg)', 'abbreviation' => 'sack', 'description' => 'Sack (50kg)', 'category_id' => $defaultCategory->id],
            ['name' => 'tons', 'display_name' => 'Metric Tons', 'abbreviation' => 'tons', 'description' => 'Metric tons', 'category_id' => $defaultCategory->id],

            // Volume units - Irrigation Supplies
            ['name' => 'l', 'display_name' => 'Liters', 'abbreviation' => 'l', 'description' => 'Liters', 'category_id' => $irrigationCategory?->id ?? $defaultCategory->id],
            ['name' => 'ml', 'display_name' => 'Milliliters', 'abbreviation' => 'ml', 'description' => 'Milliliters', 'category_id' => $irrigationCategory?->id ?? $defaultCategory->id],
            ['name' => 'gallons', 'display_name' => 'Gallons', 'abbreviation' => 'gal', 'description' => 'Gallons', 'category_id' => $irrigationCategory?->id ?? $defaultCategory->id],

            // Area units - Other
            ['name' => 'sqm', 'display_name' => 'Square Meters', 'abbreviation' => 'sqm', 'description' => 'Square meters', 'category_id' => $defaultCategory->id],
            ['name' => 'hectares', 'display_name' => 'Hectares', 'abbreviation' => 'ha', 'description' => 'Hectares', 'category_id' => $defaultCategory->id],
            ['name' => 'acres', 'display_name' => 'Acres', 'abbreviation' => 'acres', 'description' => 'Acres', 'category_id' => $defaultCategory->id],

            // Length units - Tools and Equipment
            ['name' => 'meters', 'display_name' => 'Meters', 'abbreviation' => 'm', 'description' => 'Meters', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],
            ['name' => 'cm', 'display_name' => 'Centimeters', 'abbreviation' => 'cm', 'description' => 'Centimeters', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],
            ['name' => 'feet', 'display_name' => 'Feet', 'abbreviation' => 'ft', 'description' => 'Feet', 'category_id' => $toolsAndEquipmentCategory?->id ?? $defaultCategory->id],

            // Special units - Other
            ['name' => 'pairs', 'display_name' => 'Pairs', 'abbreviation' => 'pairs', 'description' => 'Pairs', 'category_id' => $defaultCategory->id],
            ['name' => 'dozens', 'display_name' => 'Dozens', 'abbreviation' => 'doz', 'description' => 'Dozens', 'category_id' => $defaultCategory->id],
            ['name' => 'rolls', 'display_name' => 'Rolls', 'abbreviation' => 'rolls', 'description' => 'Rolls', 'category_id' => $defaultCategory->id],
            ['name' => 'sheets', 'display_name' => 'Sheets', 'abbreviation' => 'sheets', 'description' => 'Sheets', 'category_id' => $defaultCategory->id],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit['name'],
                'display_name' => $unit['display_name'],
                'abbreviation' => $unit['abbreviation'],
                'category_id' => $unit['category_id'],
                'description' => $unit['description'],
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }
    }
}
