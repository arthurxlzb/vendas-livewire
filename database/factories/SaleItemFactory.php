<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sale;
use App\Models\Product;


class SaleItemFactory extends Factory
{
    protected $model = \App\Models\SaleItem::class;

    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $price = $this->faker->randomFloat(2, 10, 100);

        return [
            'sale_id'    => Sale::factory(),    // cria venda nova se não informado
            'product_id' => Product::factory(), // cria produto novo se não informado
            'quantity'   => $quantity,
            'unit_price' => $price,
            'subtotal'   => $quantity * $price,

        ];
    }
}
