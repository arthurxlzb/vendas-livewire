<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public int   $anoSelecionado;
    public array $anosDisponiveis = [];

    public function mount()
    {
        // anos que existem
        $this->anosDisponiveis = Sale::selectRaw('YEAR(sale_date) as ano')
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano')
            ->toArray();

        $this->anoSelecionado = in_array(2025, $this->anosDisponiveis)
            ? 2025
            : ($this->anosDisponiveis[0] ?? now()->year);
    }

    public function updatedAnoSelecionado()
    {
        // re-renderiza automaticamente
    }

    public function render()
    {
        $ano = $this->anoSelecionado;

        // totais principais
        $totalReceita  = Sale::whereYear('sale_date', $ano)->sum('total');
        $totalVendas   = Sale::whereYear('sale_date', $ano)->count();
        $totalClientes = Sale::whereYear('sale_date', $ano)
            ->distinct('client_id')
            ->count('client_id');

        // vendas mensais
        $mensal = Sale::selectRaw('MONTH(sale_date) as mes, COUNT(*) as total')
            ->whereYear('sale_date', $ano)
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        $labels = $vendas = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0,0,0,$m,1));
            $vendas[] = $mensal[$m] ?? 0;
        }

        // Produto mais vendido (quantidade total)
        $produtoMaisVendido = SaleItem::select('product_id', DB::raw('SUM(quantity) as qtd'))
            ->whereHas('sale', fn($q) => $q->whereYear('sale_date', $ano))
            ->groupBy('product_id')
            ->orderByDesc('qtd')
            ->first();

        $nomeProdutoMaisVendido = $produtoMaisVendido
            ? Product::find($produtoMaisVendido->product_id)?->name ?? 'Produto não encontrado'
            : 'Sem dados';

        // Produto com maior receita (soma dos subtotais)
        $produtoComMaisReceita = SaleItem::select('product_id', DB::raw('SUM(subtotal) as receita'))
            ->whereHas('sale', fn($q) => $q->whereYear('sale_date', $ano))
            ->groupBy('product_id')
            ->orderByDesc('receita')
            ->first();

        $nomeProdutoComMaisReceita = $produtoComMaisReceita
            ? Product::find($produtoComMaisReceita->product_id)?->name ?? 'Produto não encontrado'
            : 'Sem dados';

        // Cliente que mais comprou (por total em R$)
        $clienteTop = Sale::select('client_id', DB::raw('SUM(total) as total'))
            ->whereYear('sale_date', $ano)
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->first();

        $nomeClienteTop = $clienteTop
            ? User::find($clienteTop->client_id)?->name ?? 'Cliente não encontrado'
            : 'Sem dados';

        // Venda mais cara
        $vendaMaisCara = Sale::whereYear('sale_date', $ano)
            ->orderByDesc('total')
            ->first();
        $valorVendaMaisCara = $vendaMaisCara ? $vendaMaisCara->total : 0;

        return view('Livewire.dashboard', compact(
            'totalReceita',
            'totalVendas',
            'totalClientes',
            'labels',
            'vendas',
            'nomeProdutoMaisVendido',
            'nomeProdutoComMaisReceita'
        ))->layout('layouts.app', [
            'anosDisponiveis' => $this->anosDisponiveis,
            'anoSelecionado'  => $this->anoSelecionado,
        ]);
    }
}
