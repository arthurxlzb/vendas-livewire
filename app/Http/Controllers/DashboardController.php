<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function chartData($year)
    {
        // Pega o total de vendas por mês no ano
        $monthly = Sale::selectRaw('MONTH(sale_date) as month, COUNT(*) as total')
            ->whereYear('sale_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Garante 12 meses
        $labels = $sales = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            $sales[] = $monthly[$m] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'sales' => $sales,
        ]);
    }
}
