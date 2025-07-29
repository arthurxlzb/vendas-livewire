
<div class="p-6 max-w-7xl mx-auto space-y-6">
  {{-- ano --}}
  <div class="flex justify-end">
    <select
      wire:model.live="selectedYear"
      class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring"
    >
      @foreach($availableYears as $year)
        <option value="{{ $year }}">{{ $year }}</option>
      @endforeach
    </select>
  </div>

  {{-- card --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    {{-- receita total --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Receita Total ({{ $selectedYear }})</span>
      <span class="text-3xl font-bold text-green-600">
        R$ {{ number_format($totalRevenue, 2, ',', '.') }}
      </span>
    </div>

    {{-- total de vendas --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Total de Vendas</span>
      <span class="text-3xl font-bold">{{ $totalSales }}</span>
    </div>

    {{-- clientes --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Total de Clientes</span>
      <span class="text-3xl font-bold">{{ $totalClients }}</span>
    </div>

    {{-- mais vendido --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Produto Mais Vendido ({{ $selectedYear }})</span>
      <span class="text-2xl font-semibold">{{ $topSoldName }}</span>
    </div>

    {{-- maior receita --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Produto com Maior Receita ({{ $selectedYear }})</span>
      <span class="text-2xl font-semibold">{{ $topRevenueName }}</span>
    </div>

    {{-- media por venda --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Receita Média por Venda</span>
      <span class="text-2xl font-bold text-green-600">
        R$ {{ number_format($totalSales > 0 ? $totalRevenue / $totalSales : 0, 2, ',', '.') }}
      </span>
    </div>
  </div>

  {{-- grafico --}}
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-4">Vendas Mensais {{ $selectedYear }}</h2>
    <div wire:ignore style="position: relative; height: 300px;">
      <canvas id="chartVenda" class="w-full h-full"></canvas>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  let chartInstance = null;

  async function fetchChartData(year) {
    const res = await fetch(`/api/chart-data/${year}`);
    if (!res.ok) throw new Error("Falha ao buscar dados");
    return res.json();
  }

  async function renderChart(year) {
    try {
      const { labels, sales } = await fetchChartData(year);
      const ctx = document.getElementById('chartVenda').getContext('2d');


      if (chartInstance) {
        chartInstance.destroy();
      }

      chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Sales',
            data: sales,
            backgroundColor: '#16a34a'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              max: 50,
              ticks: {
                stepSize: 5      // vai de 5 em 5
              }
            }
          },
          plugins: {
            legend: { position: 'top' }
          }
        }
      });
    } catch (error) {
      console.error("Erro ao renderizar gráfico:", error);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const selectYear = document.querySelector('select[wire\\:model\\.live="selectedYear"]');
    if (selectYear) {
      renderChart(selectYear.value);

      selectYear.addEventListener('change', () => {

        renderChart(selectYear.value);
      });
    }
  });

  Livewire.hook('message.processed', (message, component) => {
    if (component.name === 'dashboard') {
      renderChart(@this.get('selectedYear'));
    }
  });
</script>
@endpush
