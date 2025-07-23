<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition()
    {
        $unidadesValidas = ['G', 'KG', 'TON', 'ML', 'L', 'M²', 'M³', 'CM', 'M', 'KM', 'UNI'];

        return [
            'name'        => $this->faker->word(),
            'price'       => $this->faker->randomFloat(2, 1, 100),
            'description' => $this->faker->optional()->sentence(),
            'quantidade'  => $this->faker->randomFloat(2, 1, 50),
            'unidade'     => $this->faker->randomElement($unidadesValidas),
        ];
    }
}
