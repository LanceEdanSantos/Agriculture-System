<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Count units
            ['name' => 'packs/cans', 'display_name' => 'Packs/Cans', 'abbreviation' => 'packs', 'description' => 'Packs or cans'],
            ['name' => 'pcs', 'display_name' => 'Pieces', 'abbreviation' => 'pcs', 'description' => 'Pieces'],
            ['name' => 'boxes', 'display_name' => 'Boxes', 'abbreviation' => 'boxes', 'description' => 'Boxes'],
            ['name' => 'bundles', 'display_name' => 'Bundles', 'abbreviation' => 'bundles', 'description' => 'Bundles'],
            ['name' => 'sets', 'display_name' => 'Sets', 'abbreviation' => 'sets', 'description' => 'Sets'],

            // Weight units
            ['name' => 'kg', 'display_name' => 'Kilograms', 'abbreviation' => 'kg', 'description' => 'Kilograms'],
            ['name' => 'g', 'display_name' => 'Grams', 'abbreviation' => 'g', 'description' => 'Grams'],
            ['name' => 'sack', 'display_name' => 'Sack (50kg)', 'abbreviation' => 'sack', 'description' => 'Sack (50kg)'],
            ['name' => 'tons', 'display_name' => 'Metric Tons', 'abbreviation' => 'tons', 'description' => 'Metric tons'],

            // Volume units
            ['name' => 'l', 'display_name' => 'Liters', 'abbreviation' => 'l', 'description' => 'Liters'],
            ['name' => 'ml', 'display_name' => 'Milliliters', 'abbreviation' => 'ml', 'description' => 'Milliliters'],
            ['name' => 'gallons', 'display_name' => 'Gallons', 'abbreviation' => 'gal', 'description' => 'Gallons'],

            // Area units
            ['name' => 'sqm', 'display_name' => 'Square Meters', 'abbreviation' => 'sqm', 'description' => 'Square meters'],
            ['name' => 'hectares', 'display_name' => 'Hectares', 'abbreviation' => 'ha', 'description' => 'Hectares'],
            ['name' => 'acres', 'display_name' => 'Acres', 'abbreviation' => 'acres', 'description' => 'Acres'],

            // Length units
            ['name' => 'meters', 'display_name' => 'Meters', 'abbreviation' => 'm', 'description' => 'Meters'],
            ['name' => 'cm', 'display_name' => 'Centimeters', 'abbreviation' => 'cm', 'description' => 'Centimeters'],
            ['name' => 'feet', 'display_name' => 'Feet', 'abbreviation' => 'ft', 'description' => 'Feet'],

            // Special units
            ['name' => 'pairs', 'display_name' => 'Pairs', 'abbreviation' => 'pairs', 'description' => 'Pairs'],
            ['name' => 'dozens', 'display_name' => 'Dozens', 'abbreviation' => 'doz', 'description' => 'Dozens'],
            ['name' => 'rolls', 'display_name' => 'Rolls', 'abbreviation' => 'rolls', 'description' => 'Rolls'],
            ['name' => 'sheets', 'display_name' => 'Sheets', 'abbreviation' => 'sheets', 'description' => 'Sheets'],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit['name'],
                'display_name' => $unit['display_name'],
                'abbreviation' => $unit['abbreviation'],
                'description' => $unit['description'],
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }
    }
}
