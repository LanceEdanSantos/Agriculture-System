<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random category or create one if none exists
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();

        // Use a combination of multiple words and a random number to ensure uniqueness
        $name = sprintf(
            '%s %s %d',
            $this->faker->word,
            $this->faker->randomElement(['Seeds', 'Fertilizer', 'Equipment', 'Tool', 'Feed', 'Medicine']),
            $this->faker->unique()->numberBetween(1000, 9999)
        );

        return [
            'name' => $name,
            'category_id' => $category->id,
            'stock' => $this->faker->numberBetween(0, 1000),
            'minimum_stock' => $this->faker->numberBetween(10, 100),
            'description' => $this->faker->paragraph(2),
            'notes' => $this->faker->optional(0.7)->passthrough([
                $this->faker->sentence(),
                $this->faker->sentence()
            ]),
            'active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the item is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Indicate that the item is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate the category of the item.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn(array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Indicate the stock level of the item.
     */
    public function withStock(int $stock): static
    {
        return $this->state(fn(array $attributes) => [
            'stock' => $stock,
        ]);
    }
}
