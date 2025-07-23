<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProdutosIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount_loads_products_ordered_by_name()
    {
        Product::factory()->create(['name' => 'Banana']);
        Product::factory()->create(['name' => 'Abacaxi']);

        Livewire::test('produtos.index')
            ->assertSet('products.0.name', 'Abacaxi')
            ->assertSet('products.1.name', 'Banana');
    }

    public function test_open_create_modal_resets_form_and_shows_modal()
    {
        $component = Livewire::test('produtos.index')
            ->set('name', 'Teste')
            ->set('price', 10)
            ->set('description', 'Desc')
            ->set('quantidade', 5)
            ->set('unidade', 'KG');

        $component->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->assertSet('name', null)
            ->assertSet('price', null)
            ->assertSet('description', null)
            ->assertSet('quantidade', null)
            ->assertSet('unidade', null);
    }

    public function test_can_create_and_update_product()
    {
        // Create
        Livewire::test('produtos.index')
            ->set('name', 'Produto X')
            ->set('price', 20.5)
            ->set('description', 'Descrição X')
            ->set('quantidade', 2)
            ->set('unidade', 'L')
            ->call('save');

        $this->assertDatabaseHas('products', [
            'name' => 'Produto X',
            'price' => 20.5,
            'description' => 'Descrição X',
            'quantidade' => 2,
            'unidade' => 'L',
        ]);

        $product = Product::first();

        // Update
        Livewire::test('produtos.index')
            ->call('edit', $product->id)
            ->set('name', 'Produto Y')
            ->call('save');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produto Y',
        ]);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();

        Livewire::test('produtos.index')
            ->call('delete', $product->id);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
