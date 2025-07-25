<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User; // para o client_id


class SaleFactory extends Factory
{

    protected $model = \App\Models\Sale::class;

    public function definition()
    {
        return [
            'sale_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'client_id' => Client::factory(), // cria um usuário novo se não informado
            'total'     => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
