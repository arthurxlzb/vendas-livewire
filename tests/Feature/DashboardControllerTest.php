<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_chart_data_returns_all_months_with_zero_or_counts()
    {
        // Cria vendas em Janeiro(2) e Março(1) de 2025
        Sale::factory()->create(['sale_date' => '2025-01-10']);
        Sale::factory()->create(['sale_date' => '2025-01-20']);
        Sale::factory()->create(['sale_date' => '2025-03-05']);

        // Chama a rota de chartData (prefixo /api)
        $response = $this->getJson("/api/chart-data/2025");

        $response->assertStatus(200)
            ->assertJsonCount(12, 'labels')
            ->assertJsonCount(12, 'vendas');

        $json = $response->json();
        // Verifica formatos e valores específicos
        $this->assertEquals('Jan', $json['labels'][0]);
        $this->assertEquals('Mar', $json['labels'][2]);
        $this->assertEquals(2, $json['vendas'][0]);
        $this->assertEquals(1, $json['vendas'][2]);
        // Meses sem vendas devem ser zero
        $this->assertEquals(0, $json['vendas'][1]); // Feb
        $this->assertEquals(0, $json['vendas'][11]); // Dec
    }

    public function test_chart_data_with_year_without_sales_returns_all_zeros()
    {
        // Nenhuma venda para 2030
        $response = $this->getJson("/api/chart-data/2030");

        $response->assertStatus(200)
            ->assertJson([ 'vendas' => array_fill(0,12,0) ]);
    }
}
