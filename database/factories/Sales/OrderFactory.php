<?php

namespace Database\Factories\Sales;

use App\Models\Sales\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'FC-' . str_pad((string) $this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'public_token' => Str::random(48),
            'full_name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->numerify('+961########'),
            'city' => $this->faker->randomElement(['Beirut', 'Tripoli', 'Saida', 'Jounieh']),
            'address' => $this->faker->address(),
            'notes' => $this->faker->optional()->sentence(),
            'subtotal' => $this->faker->randomFloat(2, 20, 300),
            'discount_total' => $this->faker->randomFloat(2, 0, 50),
            'total' => $this->faker->randomFloat(2, 20, 280),
            'currency' => 'USD',
            'order_status' => $this->faker->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_DELIVERED,
            ]),
            'placed_at' => now()->subDays($this->faker->numberBetween(0, 15)),
        ];
    }
}
