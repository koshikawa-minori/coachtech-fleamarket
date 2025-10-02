<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Item::class;

    public function definition(): array
    {
        return [
        //
            'seller_user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'brand_name' => $this->faker->optional()->company(),
            'price' => $this->faker->numberBetween(300, 50000),
            'description' => $this->faker->text(120),
            'condition' => $this->faker->randomElement([1,2,3,4]),
            'image_path' => 'https://via.placeholder.com/300x300.png?text=DemoImage',
            'is_sold' => false,
        ];
    }
}
