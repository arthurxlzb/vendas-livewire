<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\User;

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

        $this->anoSelecionado = $this->anosDisponiveis[0] ?? now()->year;
    }

    public function updatedAnoSelecionado()
    {
        // apenas re-renderiza a view com os novos dados
    }

    public function render()
    {
        $ano = $this->anoSelecionado;

        // totais
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

        // prepara arrays
        $labels = $vendas = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0,0,0,$m,1));
            $vendas[] = $mensal[$m] ?? 0;
        }

        return view('Livewire.dashboard', compact(
            'totalReceita', 'totalVendas', 'totalClientes',
            'labels', 'vendas'
        ))->layout('layouts.app', [
            'anosDisponiveis' => $this->anosDisponiveis,
            'anoSelecionado'  => $this->anoSelecionado,
        ]);
    }
}
