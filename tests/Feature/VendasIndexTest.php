<?php

namespace Tests\Feature;

use App\Livewire\Vendas\Index;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class VendasIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount_carrega_clients_products_and_sales()
    {
        $client  = User::factory()->create(['name' => 'Cliente Teste']);
        $product = Product::factory()->create(['name' => 'Prod Teste']);
        $sale    = Sale::factory()->create([
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
            ->assertSet('clients.0.id',  $client->id)
            ->assertSet('products.0.id', $product->id)
            ->assertSet('sales.0.id',    $sale->id);
    }

    public function test_add_and_remove_items_and_calculate_total()
    {
        $component = Livewire::test('vendas.index');

        // 1 item por padrão
        $this->assertCount(1, $component->get('items'));

        // adiciona
        $component->call('addItem');
        $this->assertCount(2, $component->get('items'));

        // define produto e quantidade
        $product = Product::factory()->create(['price' => 20]);
        $component
            ->set('items.0.product_id', $product->id)
            ->set('items.0.quantity',   3);

        $this->assertEquals(60, $component->get('items')[0]['subtotal']);
        $this->assertEquals(60, $component->get('total'));

        // remove
        $component->call('removeItem', 1);
        $this->assertCount(1, $component->get('items'));
    }

    public function test_create_new_sale_and_persist_to_database()
    {
        $client  = User::factory()->create();
        $product = Product::factory()->create(['price' => 15]);

        Livewire::test('vendas.index')
            ->set('clientId', $client->id)
            ->set('saleDate', Carbon::parse('2025-07-23')->toDateString())
            ->set('items', [[
                'product_id' => $product->id,
                'quantity'   => 2,
                'unit_price' => 15,
                'subtotal'   => 30,
            ]])
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

    public function test_refresh_values_recalcula_subtotais_e_total()
    {
        // cria um produto com preço 10
        $product = Product::factory()->create(['price' => 10]);

        $component = Livewire::test('vendas.index')
            // injeta dados incorretos
            ->set('items', [[
                'product_id' => $product->id,
                'quantity'   => 3,
                'unit_price' => 5,   // errado
                'subtotal'   => 15,  // errado
            ]])
            ->set('total', 15)
            ->call('refreshValues');

        // agora unit_price deve ser 10 e subtotal 30
        $items = $component->get('items');
        $this->assertEquals(10, $items[0]['unit_price']);
        $this->assertEquals(30, $items[0]['subtotal']);
        $this->assertEquals(30, $component->get('total'));
    }

    public function test_edit_carrega_dados_para_edicao()
    {
        $client  = User::factory()->create();
        $product = Product::factory()->create(['price' => 30]);

        $sale = Sale::factory()->create([
            'client_id' => $client->id,
            'sale_date' => '2025-07-01',
            'total'     => 60,
        ]);

        SaleItem::factory()->create([
            'sale_id'    => $sale->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 30,
            'subtotal'   => 60,
        ]);

        Livewire::test('vendas.index')
            ->call('edit', $sale->id)
            ->assertSet('saleId',   $sale->id)
            ->assertSet('clientId', $client->id)
            ->assertSet('saleDate', '2025-07-01')
            ->assertSet('total',    60)
            ->assertSet('items.0.product_id', $product->id)
            ->assertSet('items.0.quantity',   2)
            ->assertSet('items.0.unit_price', 30)
            ->assertSet('items.0.subtotal',   60);
    }

    public function test_view_and_close_details_modal()
    {
        $sale = Sale::factory()->create();

        Livewire::test('vendas.index')
            ->call('viewDetails', $sale->id)
            ->assertSet('viewingSale.id', $sale->id)
            ->call('closeDetails')
            ->assertSet('viewingSale', null);
    }

    public function test_open_create_modal_sets_default_and_today_date()
    {
        $today = Carbon::today()->toDateString();

        Livewire::test('vendas.index')
            // coloca valores para depois resetar
            ->set('clientId', 5)
            ->set('items', [[
                'product_id' => 3,
                'quantity'   => 2,
                'unit_price' => 10,
                'subtotal'   => 20,
            ]])
            ->set('total', 20)
            ->call('openCreateModal')
            ->assertSet('saleId',   null)
            ->assertSet('clientId', '')
            ->assertSet('saleDate', $today)
            ->assertSet('items', [[
                'product_id' => null,
                'quantity'   => 1,
                'unit_price' => 0,
                'subtotal'   => 0,
            ]])
            ->assertSet('total', 0);
    }

    public function test_save_valida_campos_obrigatorios()
    {
        Livewire::test(Index::class)
            ->set('clientId', null)
            ->set('saleDate', null)
            ->set('items', []) // array vazio para disparar erro customizado
            ->call('save')
            ->assertHasErrors([
                'clientId' => 'required',
                'saleDate' => 'required',
                'items'    => true, // qualquer erro em items (custom)
            ]);
    }

    // Novo teste para cobrir atualização (edição) da venda, garantindo 100% cobertura nas linhas 138-140
   public function test_update_existing_sale_and_persist_changes()
{
    $client1  = User::factory()->create();
    $client2  = User::factory()->create();
    $product1 = Product::factory()->create(['price' => 10]);
    $product2 = Product::factory()->create(['price' => 20]);

    $sale = Sale::factory()->create([
        'client_id' => $client1->id,
        'sale_date' => '2025-07-20',
        'total'     => 10,
    ]);

    $sale->items()->create([
        'product_id' => $product1->id,
        'quantity'   => 1,
        'unit_price' => 10,
        'subtotal'   => 10,
    ]);

    // Executa a atualização da venda
    $component = Livewire::test('vendas.index')
        ->set('saleId',   $sale->id)
        ->set('clientId', $client2->id)
        ->set('saleDate', '2025-07-21')
        ->set('items', [[
            'product_id' => $product2->id,
            'quantity'   => 2,
            'unit_price' => 20,
            'subtotal'   => 40,
        ]])
        ->call('save')
        // verifica que, após salvar, o modal foi fechado e o formulário resetado
        ->assertSet('showCreateModal', false)
        ->assertSet('saleId', null)
        ->assertSet('clientId', null)
        ->assertSet('items', [[
            'product_id' => null,
            'quantity'   => 1,
            'unit_price' => 0,
            'subtotal'   => 0,
        ]])
        ->assertSet('total', 0);

    // Valida que os dados da venda foram atualizados no banco
    $this->assertDatabaseHas('sales', [
        'id'        => $sale->id,
        'client_id' => $client2->id,
        'sale_date' => '2025-07-21',
        'total'     => 40,
    ]);

    // Valida que os itens antigos foram removidos e o novo inserido
    $this->assertDatabaseHas('sale_items', [
        'sale_id'    => $sale->id,
        'product_id' => $product2->id,
        'quantity'   => 2,
        'unit_price' => 20,
        'subtotal'   => 40,
    ]);
    $this->assertDatabaseMissing('sale_items', [
        'sale_id'    => $sale->id,
        'product_id' => $product1->id,
    ]);
}




}
