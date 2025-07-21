<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition()
    {
        return [
            'name'        => $this->faker->word(),
            'price'       => $this->faker->randomFloat(2, 1, 100), // entre R$1 e R$100
            'description' => $this->faker->optional()->sentence(),
            'quantidade'  => $this->faker->randomFloat(2, 1, 50),  // se já tiver esse campo
            'unidade'     => $this->faker->randomElement(['g','kg','ml','un']),
        ];
    }
}
