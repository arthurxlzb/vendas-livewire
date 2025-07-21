<div class="p-6 max-w-6xl mx-auto space-y-6">
  {{-- Seletor de ano --}}
  <div class="flex justify-end">
    <select wire:model.live="anoSelecionado" class="border rounded px-3 py-1 text-sm">
      @foreach($anosDisponiveis as $ano)
        <option value="{{ $ano }}">{{ $ano }}</option>
      @endforeach
    </select>
  </div>

  {{-- Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-5 auto-rows-fr">

    <div class="p-6 bg-white rounded shadow flex flex-col justify-between h-34">
      <p class="text-gray-600 text-sm break-words">Receita Total ({{ $anoSelecionado }})</p>
      <p class="text-3xl font-bold text-green-600">
        R$ {{ number_format($totalReceita, 2, ',', '.') }}
      </p>
    </div>

    <div class="p-6 bg-white rounded shadow flex flex-col justify-between h-34">
      <p class="text-gray-600 text-sm break-words">Total de Vendas</p>
      <p class="text-3xl font-bold">{{ $totalVendas }}</p>
    </div>

    <div class="p-6 bg-white rounded shadow flex flex-col justify-between h-34">
      <p class="text-gray-600 text-sm break-words">Total de Clientes</p>
      <p class="text-3xl font-bold">{{ $totalClientes }}</p>
    </div>

    <div class="p-6 bg-white rounded shadow flex flex-col justify-between h-34">
      <p class="text-gray-600 text-sm break-words">Produto Mais Vendido ({{ $anoSelecionado }})</p>
      <p class="text-2xl font-semibold text-blue-400">{{ $nomeProdutoMaisVendido }}</p>
    </div>

    <div class="p-6 bg-white rounded shadow flex flex-col justify-between h-34">
      <p class="text-gray-600 text-sm break-words">Produto com Maior Receita ({{ $anoSelecionado }})</p>
      <p class="text-2xl font-semibold text-blue-400">{{ $nomeProdutoComMaisReceita }}</p>
    </div>

    <div class="p-6 bg-white rounded shadow flex flex-col justify-between h-34">
      <p class="text-gray-500 text-sm break-words">Receita Média por Venda</p>
      <p class="text-2xl font-bold text-indigo-600">
        R$ {{ number_format($totalVendas > 0 ? $totalReceita / $totalVendas : 0, 2, ',', '.') }}
      </p>
    </div>

  </div>
</div>
