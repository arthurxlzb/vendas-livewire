<div class="p-6 max-w-7xl mx-auto space-y-6">
  {{-- Seletor de ano --}}
  <div class="flex justify-end">
    <select wire:model.live="anoSelecionado"
            class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring">
      @foreach($anosDisponiveis as $ano)
        <option value="{{ $ano }}">{{ $ano }}</option>
      @endforeach
    </select>
  </div>

  {{-- Grid de Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    {{-- Receita Total --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Receita Total ({{ $anoSelecionado }})</span>
      <span class="text-3xl font-bold text-green-600">
        R$ {{ number_format($totalReceita, 2, ',', '.') }}
      </span>
    </div>

    {{-- Total de Vendas --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Total de Vendas</span>
      <span class="text-3xl font-bold">{{ $totalVendas }}</span>
    </div>

    {{-- Total de Clientes --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Total de Clientes</span>
      <span class="text-3xl font-bold">{{ $totalClientes }}</span>
    </div>

    {{-- Produto Mais Vendido --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Produto Mais Vendido ({{ $anoSelecionado }})</span>
      <span class="text-2xl font-semibold text-blue-500">{{ $nomeProdutoMaisVendido }}</span>
    </div>

    {{-- Produto com Maior Receita --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Produto com Maior Receita ({{ $anoSelecionado }})</span>
      <span class="text-2xl font-semibold text-blue-500">{{ $nomeProdutoComMaisReceita }}</span>
    </div>

    {{-- Receita Média por Venda --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between min-h-[140px]">
      <span class="text-gray-600 text-sm">Receita Média por Venda</span>
      <span class="text-2xl font-bold text-indigo-600">
        R$ {{ number_format($totalVendas > 0 ? $totalReceita / $totalVendas : 0, 2, ',', '.') }}
      </span>
    </div>
  </div>



  <div class="bg-white rounded-lg shadow p-6">
  <h2 class="text-xl font-semibold mb-4">Vendas Mensais {{ $anoSelecionado }}</h2>
  <div style="position: relative; height: 300px;">
    <canvas id="chartVenda" class="w-full h-full"></canvas>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  let chartInstance = null;

  async function fetchChartData(ano) {
    const res = await fetch(`/api/chart-data/${ano}`);
    if (!res.ok) throw new Error("Falha ao buscar dados");
    return res.json();
  }

  async function renderChart(ano) {
    try {
      const { labels, vendas } = await fetchChartData(ano);
      const ctx = document.getElementById('chartVenda').getContext('2d');

      // Se já existe um chart, destrua antes de criar
      if (chartInstance) {
        chartInstance.destroy();
      }

      chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Vendas',
            data: vendas,
            backgroundColor: '#3b82f6'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              max: 30,
              ticks: { stepSize: 5 }
            }
          },
          plugins: { legend: { position: 'top' } }
        }
      });
    } catch (error) {
      console.error("Erro ao renderizar gráfico:", error);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const selectAno = document.querySelector('select[wire\\:model\\.live="anoSelecionado"]');
    // Primeiro draw com o ano carregado pelo Blade/Livewire
    renderChart(selectAno.value);

    // Ao mudar o select, atualiza o gráfico
    selectAno.addEventListener('change', () => {
      renderChart(selectAno.value);
    });
  });
</script>
@endpush
