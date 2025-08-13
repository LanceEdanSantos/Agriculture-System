<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Assorted Vegetable Seeds',
                'description' => 'Various types of vegetable seeds for planting',
                'color' => '#10B981', // Green
                'icon' => '🌱',
                'sort_order' => 1,
            ],
            [
                'name' => 'Organic Agriculture Development',
                'description' => 'Organic farming materials and development tools',
                'color' => '#059669', // Dark Green
                'icon' => '🌿',
                'sort_order' => 2,
            ],
            [
                'name' => 'Fertilizers',
                'description' => 'Various types of fertilizers for crop nutrition',
                'color' => '#F59E0B', // Amber
                'icon' => '💩',
                'sort_order' => 3,
            ],
            [
                'name' => 'Pesticides',
                'description' => 'Pest control and protection materials',
                'color' => '#DC2626', // Red
                'icon' => '🦗',
                'sort_order' => 4,
            ],
            [
                'name' => 'Tools and Equipment',
                'description' => 'Farming tools and agricultural equipment',
                'color' => '#6B7280', // Gray
                'icon' => '🔧',
                'sort_order' => 5,
            ],
            [
                'name' => 'Plastic Materials',
                'description' => 'Plastic bags, covers, and packaging materials',
                'color' => '#3B82F6', // Blue
                'icon' => '📦',
                'sort_order' => 6,
            ],
            [
                'name' => 'Irrigation Supplies',
                'description' => 'Watering and irrigation system materials',
                'color' => '#0EA5E9', // Sky Blue
                'icon' => '💧',
                'sort_order' => 7,
            ],
            [
                'name' => 'Soil Amendments',
                'description' => 'Soil improvement and conditioning materials',
                'color' => '#8B5CF6', // Purple
                'icon' => '🌍',
                'sort_order' => 8,
            ],
            [
                'name' => 'Seedlings and Plants',
                'description' => 'Young plants and seedlings ready for transplanting',
                'color' => '#84CC16', // Lime
                'icon' => '🌱',
                'sort_order' => 9,
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous agricultural supplies',
                'color' => '#6B7280', // Gray
                'icon' => '📋',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 