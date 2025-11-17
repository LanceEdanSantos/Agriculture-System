<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Farm;
use App\Models\Item;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            'Seeds',
            'Fertilizers',
            'Pesticides',
            'Equipment',
            'Tools',
            'Irrigation',
            'Feeds',
            'Veterinary',
            'Packaging',
            'Other'
        ];

        foreach ($categories as $categoryName) {
            Category::factory()->create([
                'name' => $categoryName,
                'description' => "Items related to {$categoryName}",
                'active' => true,
            ]);
        }

        // Create 10 farms
        $farms = Farm::factory(10)->create();

        // Create 200 items and associate them with random farms
        Item::factory(200)
            ->create();

        $this->command->info('Test data seeded successfully!');
        $this->command->info('- 10 farms created');
        $this->command->info('- 200 items created with random category and farm associations');
        $this->command->info('- 10 categories created');
    }
}
