<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount_carrega_anos_e_ano_selecionado()
    {
        // Cria vendas em 2024 e 2025
        Sale::factory()->create(['sale_date' => '2024-06-01']);
        Sale::factory()->create(['sale_date' => '2025-07-01']);

        Livewire::test('dashboard')
            ->assertSet('anosDisponiveis', [2025, 2024]) // ordenado desc
            ->assertSet('anoSelecionado', 2025);          // 2025 forçado se existir
    }

    public function test_load_chart_data_e_updatedAnoSelecionado()
    {
        // Uma venda em 2024 (Janeiro) e uma em 2025 (Fevereiro)
        Sale::factory()->create(['sale_date' => '2024-01-15']);
        Sale::factory()->create(['sale_date' => '2025-02-15']);

        $component = Livewire::test('dashboard');

        // Inicialmente anoSelecionado = 2025 => vendas em Fevereiro
        $component->assertSet('labels', [
            'Jan','Feb','Mar','Apr','May','Jun',
            'Jul','Aug','Sep','Oct','Nov','Dec',
        ]);
        $component->assertSet('vendas', [
            0, // Jan
            1, // Feb
            0,0,0,0,0,0,0,0,0,0,
        ]);

        // Ao trocar para 2024, vendas devem refletir Janeiro
        $component->set('anoSelecionado', 2024);
        $component->assertSet('vendas', [
            1, // Jan
            0, // Feb
            0,0,0,0,0,0,0,0,0,0,
        ]);
    }

    public function test_render_retorna_view_com_valores_corretos()
    {
        $client = User::factory()->create();

        $productA = Product::factory()->create(['name' => 'Produto A']);
        $productB = Product::factory()->create(['name' => 'Produto B']);

        $sale1 = Sale::factory()->create([
            'sale_date' => '2025-05-01',
            'client_id' => $client->id,
            'total'     => 100,
        ]);
        $sale2 = Sale::factory()->create([
            'sale_date' => '2025-06-01',
            'client_id' => $client->id,
            'total'     => 150,
        ]);

        // Inclui unit_price para campos não-nullable
        SaleItem::factory()->create([
            'sale_id'    => $sale1->id,
            'product_id' => $productA->id,
            'quantity'   => 2,
            'unit_price' => 20,
            'subtotal'   => 40,
        ]);
        SaleItem::factory()->create([
            'sale_id'    => $sale2->id,
            'product_id' => $productB->id,
            'quantity'   => 3,
            'unit_price' => 30,
            'subtotal'   => 90,
        ]);

        Livewire::test('dashboard')
            ->set('anoSelecionado', 2025)
            ->assertViewHas('totalReceita', 250)
            ->assertViewHas('totalVendas', 2)
            ->assertViewHas('totalClientes', 1)
            ->assertViewHas('nomeProdutoMaisVendido', 'Produto B')
            ->assertViewHas('nomeProdutoComMaisReceita', 'Produto B')
            ->assertViewHas('labels')
            ->assertViewHas('vendas');
    }
}
