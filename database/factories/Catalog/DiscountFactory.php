<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([Discount::TYPE_PERCENTAGE, Discount::TYPE_FIXED]);

        return [
            'name' => ucfirst($this->faker->words(3, true)) . ' Discount',
            'code' => strtoupper($this->faker->optional()->bothify('OFF##??')),
            'type' => $type,
            'value' => $type === Discount::TYPE_PERCENTAGE
                ? $this->faker->numberBetween(5, 30)
                : $this->faker->randomFloat(2, 3, 20),
            'scope' => $this->faker->randomElement([
                Discount::SCOPE_GLOBAL,
                Discount::SCOPE_PRODUCT,
                Discount::SCOPE_CATEGORY,
            ]),
            'start_date' => now()->subDays($this->faker->numberBetween(1, 10)),
            'end_date' => now()->addDays($this->faker->numberBetween(5, 30)),
            'is_active' => true,
            'priority' => $this->faker->numberBetween(1, 10),
        ];
    }
}
