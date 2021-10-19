<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
			'name' => $this->faker->name,
			'description' => $this->faker->name,
			'price' => $this->faker->randomDigit,
			'image' => $this->faker->image,
        ];
    }
}
