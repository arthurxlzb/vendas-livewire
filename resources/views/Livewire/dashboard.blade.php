<div class="p-6 max-w-4xl mx-auto space-y-6">
  {{-- Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="p-4 bg-white rounded shadow">
      <p class="text-gray-600">Receita Total ({{ $anoSelecionado }})</p>
      <p class="text-2xl font-bold text-green-600">
        R$ {{ number_format($totalReceita, 2, ',', '.') }}
      </p>
    </div>
    <div class="p-4 bg-white rounded shadow">
      <p class="text-gray-600">Total de Vendas</p>
      <p class="text-2xl font-bold">{{ $totalVendas }}</p>
    </div>
    <div class="p-4 bg-white rounded shadow">
      <p class="text-gray-600">Total de Clientes</p>
      <p class="text-2xl font-bold">{{ $totalClientes }}</p>
    </div>
  </div>

 
