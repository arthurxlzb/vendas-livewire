<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public int   $anoSelecionado;
    public array $anosDisponiveis = [];
    public array $labels         = [];
    public array $vendas         = [];

    public function mount()
{
    // anos disponíveis
    $this->anosDisponiveis = Sale::selectRaw('YEAR(sale_date) as ano')
        ->distinct()
        ->orderByDesc('ano')
        ->pluck('ano')
        ->toArray();

    // força 2025 como padrão se existir
    $this->anoSelecionado = in_array(2025, $this->anosDisponiveis)
        ? 2025
        : ($this->anosDisponiveis[0] ?? now()->year);

    $this->loadChartData();
}

    public function updatedAnoSelecionado()
    {
        $this->loadChartData();
    }

    private function loadChartData(): void
    {
        $mensal = Sale::selectRaw('MONTH(sale_date) as mes, COUNT(*) as total')
            ->whereYear('sale_date', $this->anoSelecionado)
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        $this->labels = $this->vendas = [];
        for ($m = 1; $m <= 12; $m++) {
            $this->labels[] = date('M', mktime(0, 0, 0, $m, 1));
            $this->vendas[] = $mensal[$m] ?? 0;
        }
    }

    public function render()
    {
        $ano = $this->anoSelecionado;

        $totalReceita  = Sale::whereYear('sale_date', $ano)->sum('total');
        $totalVendas   = Sale::whereYear('sale_date', $ano)->count();
        $totalClientes = Sale::whereYear('sale_date', $ano)
            ->distinct('client_id')->count('client_id');

        $produtoMaisVendido = SaleItem::select('product_id', DB::raw('SUM(quantity) as qtd'))
            ->whereHas('sale', fn($q) => $q->whereYear('sale_date', $ano))
            ->groupBy('product_id')->orderByDesc('qtd')->first();
        $nomeProdutoMaisVendido = $produtoMaisVendido
            ? Product::find($produtoMaisVendido->product_id)->name
            : 'Sem dados';

        $produtoComMaisReceita = SaleItem::select('product_id', DB::raw('SUM(subtotal) as receita'))
            ->whereHas('sale', fn($q) => $q->whereYear('sale_date', $ano))
            ->groupBy('product_id')->orderByDesc('receita')->first();
        $nomeProdutoComMaisReceita = $produtoComMaisReceita
            ? Product::find($produtoComMaisReceita->product_id)->name
            : 'Sem dados';

        // Variáveis locais para compact
        $labels = $this->labels;
        $vendas = $this->vendas;

        return view('Livewire.dashboard', compact(
            'totalReceita',
            'totalVendas',
            'totalClientes',
            'nomeProdutoMaisVendido',
            'nomeProdutoComMaisReceita',
            'labels',
            'vendas'
        ))
        ->layout('layouts.app', [
            'anosDisponiveis' => $this->anosDisponiveis,
            'anoSelecionado'  => $this->anoSelecionado,
        ]);
    }
}
