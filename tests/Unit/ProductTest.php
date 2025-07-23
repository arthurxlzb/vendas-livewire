<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_relation_sale_items()
    {
        $product = Product::factory()->create();
        SaleItem::factory()->create(['product_id' => $product->id]);
        $this->assertTrue($product->saleItems->first() instanceof SaleItem);
        $this->assertCount(1, $product->saleItems);
    }

    public function test_fillable_fields()
    {
        $fillable = (new Product())->getFillable();
        $this->assertEquals(['name','price','description','quantidade','unidade'], $fillable);
    }
}
