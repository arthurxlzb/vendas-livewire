<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function chartData($ano)
    {
        // Pega o total de vendas por mês no ano
        $mensal = Sale::selectRaw('MONTH(sale_date) as mes, COUNT(*) as total')
            ->whereYear('sale_date', $ano)
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Garante 12 meses
        $labels = $vendas = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            $vendas[] = $mensal[$m] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'vendas' => $vendas,
        ]);
    }
}
