<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([Coupon::TYPE_PERCENTAGE, Coupon::TYPE_FIXED]);

        return [
            'code' => strtoupper($this->faker->unique()->bothify('FIT##??')),
            'type' => $type,
            'value' => $type === Coupon::TYPE_PERCENTAGE
                ? $this->faker->numberBetween(5, 20)
                : $this->faker->randomFloat(2, 3, 15),
            'minimum_order_amount' => $this->faker->randomFloat(2, 0, 60),
            'usage_limit' => $this->faker->numberBetween(20, 200),
            'usage_per_customer' => $this->faker->numberBetween(1, 3),
            'used_count' => 0,
            'start_date' => now()->subDays(3),
            'end_date' => now()->addDays(45),
            'is_active' => true,
        ];
    }
}
