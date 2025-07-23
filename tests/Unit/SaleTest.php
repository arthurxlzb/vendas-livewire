<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Sale;
use App\Models\User;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_relation_client_and_items_and_casts()
    {
        $user = User::factory()->create();
        $sale = Sale::factory()->create([
            'client_id' => $user->id,
            'sale_date' => '2025-01-02',
            'total'     => 123.45,
        ]);
        $item = SaleItem::factory()->create(['sale_id' => $sale->id]);

        // belongsTo client
        $this->assertTrue($sale->client instanceof User);
        $this->assertEquals($user->id, $sale->client->id);

        // hasMany items
        $this->assertTrue($sale->items->first() instanceof SaleItem);

        // Casts
        $this->assertInstanceOf(Carbon::class, $sale->sale_date);
        $this->assertIsFloat($sale->total);
    }
}
