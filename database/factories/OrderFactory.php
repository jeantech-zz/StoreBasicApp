<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
			'user_id' => $this->faker->name,
			'customer_name' => $this->faker->name,
			'customer_email' => $this->faker->name,
			'customer_mobile' => $this->faker->name,
			'product_id' => $this->faker->name,
			'status' => $this->faker->name,
        ];
    }
}
