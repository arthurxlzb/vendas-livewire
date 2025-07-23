<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class VendasIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount_carrega_clients_products_and_sales()
    {
        $client = User::factory()->create(['name' => 'Cliente Teste']);
        $product = Product::factory()->create(['name' => 'Prod Teste']);
        $sale = Sale::factory()->create([
            'client_id' => $client->id,
            'sale_date' => '2025-07-01',
            'total'     => 100,
        ]);
        SaleItem::factory()->create([
            'sale_id'    => $sale->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 50,
            'subtotal'   => 100,
        ]);

        Livewire::test('vendas.index')
            ->assertSet('clients.0.id', $client->id)
            ->assertSet('products.0.id', $product->id)
            ->assertSet('sales.0.id', $sale->id);
    }

    public function test_add_and_remove_items_and_calculate_total()
    {
        $component = Livewire::test('vendas.index');

        // Initially one empty item
        $this->assertCount(1, $component->get('items'));

        // Add an item
        $component->call('addItem');
        $this->assertCount(2, $component->get('items'));

        // Create a product and update item
        $product = Product::factory()->create(['price' => 20]);
        $component->set('items.0.product_id', $product->id)
                  ->set('items.0.quantity', 3);

        // Check subtotal and total
        $this->assertEquals(60, $component->get('items')[0]['subtotal']);
        $this->assertEquals(60, $component->get('total'));

        // Remove the second item
        $component->call('removeItem', 1);
        $this->assertCount(1, $component->get('items'));
    }

    public function test_create_new_sale_and_persist_to_database()
    {
        $client = User::factory()->create();
        $product = Product::factory()->create(['price' => 15]);

        Livewire::test('vendas.index')
            ->set('clientId', $client->id)
            ->set('saleDate', '2025-07-23')
            ->set('items', [
                ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 15, 'subtotal' => 30]
            ])
            ->call('save');

        $this->assertDatabaseHas('sales', [
            'client_id' => $client->id,
            'sale_date' => '2025-07-23',
            'total'     => 30,
        ]);
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'quantity'   => 2,
            'subtotal'   => 30,
        ]);
    }

    public function test_delete_a_sale()
    {
        $sale = Sale::factory()->create();
        Livewire::test('vendas.index')
            ->call('delete', $sale->id);

        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
    }
}
