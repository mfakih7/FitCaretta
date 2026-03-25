<?php

namespace Database\Factories\Catalog;

use App\Enums\ProductGender;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst($this->faker->words(3, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'sku' => 'FC-' . strtoupper($this->faker->unique()->bothify('???###')),
            'short_description' => $this->faker->sentence(10),
            'description' => $this->faker->paragraph(3),
            'gender_target' => $this->faker->randomElement([
                ProductGender::MEN->value,
                ProductGender::WOMEN->value,
                ProductGender::UNISEX->value,
            ]),
            'base_price' => $this->faker->randomFloat(2, 18, 150),
            'sale_price' => null,
            'is_active' => true,
            'is_featured' => $this->faker->boolean(30),
            'is_new_arrival' => $this->faker->boolean(35),
            'main_image_path' => null,
        ];
    }
}
