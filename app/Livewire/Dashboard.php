<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public int   $selectedYear;
    public array $availableYears = [];
    public array $labels         = [];
    public array $salesData      = [];

    public function mount(): void
    {
        // anos disponíveis
        $this->availableYears = Sale::selectRaw('YEAR(sale_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        // padrão: 2025 se existir, senão primeiro disponível ou ano atual
        $this->selectedYear = in_array(2025, $this->availableYears)
            ? 2025
            : ($this->availableYears[0] ?? now()->year);

        $this->loadChartData();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadChartData();
    }

    private function loadChartData(): void
    {
        $monthly = Sale::selectRaw('MONTH(sale_date) as month, COUNT(*) as total')
            ->whereYear('sale_date', $this->selectedYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $this->labels    = [];
        $this->salesData = [];

        for ($m = 1; $m <= 12; $m++) {
            $this->labels[]    = Carbon::create(null, $m)->format('M');
            $this->salesData[] = $monthly[$m] ?? 0;
        }
    }

    public function render()
    {
        $year = $this->selectedYear;

        $totalRevenue  = Sale::whereYear('sale_date', $year)->sum('total');
        $totalSales    = Sale::whereYear('sale_date', $year)->count();
        $totalClients  = Sale::whereYear('sale_date', $year)
            ->distinct('client_id')
            ->count('client_id');

        $topSold = SaleItem::select('product_id', DB::raw('SUM(quantity) as qty'))
            ->whereHas('sale', fn($q) => $q->whereYear('sale_date', $year))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->first();

        $topRevenue = SaleItem::select('product_id', DB::raw('SUM(subtotal) as revenue'))
            ->whereHas('sale', fn($q) => $q->whereYear('sale_date', $year))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->first();

        $topSoldName    = $topSold    ? Product::find($topSold->product_id)->name : 'No data';
        $topRevenueName = $topRevenue ? Product::find($topRevenue->product_id)->name : 'No data';

        return view('livewire.dashboard', [
            'totalRevenue'      => $totalRevenue,
            'totalSales'        => $totalSales,
            'totalClients'      => $totalClients,
            'topSoldName'       => $topSoldName,
            'topRevenueName'    => $topRevenueName,
        ])
        ->with([
            'labels'    => $this->labels,
            'salesData'=> $this->salesData,
        ])
        ->layout('layouts.app', [
            'availableYears'  => $this->availableYears,
            'selectedYear'    => $this->selectedYear,
        ]);
    }
}
