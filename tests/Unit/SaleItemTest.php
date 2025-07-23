<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SaleItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_relations_and_casts()
    {
        $sale = Sale::factory()->create();
        $product = Product::factory()->create();
        $item = SaleItem::factory()->create([
            'sale_id'    => $sale->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 10.5,
            'subtotal'   => 21.0,
        ]);

        $this->assertTrue($item->sale instanceof Sale);
        $this->assertTrue($item->product instanceof Product);
        $this->assertIsFloat($item->quantity);
        $this->assertIsFloat($item->unit_price);
        $this->assertIsFloat($item->subtotal);
    }
}
