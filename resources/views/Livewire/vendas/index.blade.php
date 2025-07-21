<div class="p-6 max-w-5xl mx-auto">
    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between mt-5">
        <h2 class="text-4xl font-bold">Vendas</h2>
        <button wire:click="openCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white hover:text-black px-5 py-3 rounded">
            Registrar Venda
        </button>
    </div>

    {{-- Mensagem de sucesso --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Modal de Criação/Edição --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 backdrop-blur-sm"></div>

            <div class="relative bg-blue-300 border-2 border-black rounded-lg shadow-lg p-6 z-10 max-w-4xl w-full">
                <h2 class="text-xl font-bold mb-4">{{ $saleId ? 'Editar Venda' : 'Registrar Venda' }}</h2>

                {{-- Formulário --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    {{-- Cliente --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select wire:model="clientId" class="mt-1 block w-full border rounded p-2">
                            <option value="">Selecione...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        @error('clientId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Data --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data da Venda</label>
                        <input type="date" wire:model="saleDate" class="mt-1 block w-full border rounded p-2">
                        @error('saleDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Tabela de Itens --}}
                <div class="overflow-x-auto mb-4">
                    <table class="w-full table-auto border border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 border text-left">Produto</th>
                                <th class="p-2 border text-center">Qtd</th>
                                <th class="p-2 border text-center">Valor Unitário</th>
                                <th class="p-2 border text-center">Subtotal</th>
                                <th class="p-2 border text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr wire:key="item-{{ $index }}">
                                    <td class="p-2 border">
                                        <select wire:model="items.{{ $index }}.product_id" class="w-full border rounded p-1">
                                            <option value="">Selecione...</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("items.$index.product_id") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="p-2 border text-center">
                                        <input type="number" min="1" step="1" wire:model="items.{{ $index }}.quantity" class="w-20 text-center border rounded p-1">
                                        @error("items.$index.quantity") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="p-2 border text-center">
                                        R$ {{ number_format($item['unit_price'] ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="p-2 border text-center">
                                        R$ {{ number_format($item['subtotal'] ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="p-2 border text-center">
                                        <button wire:click="removeItem({{ $index }})" class="bg-red-800 text-white hover:text-black px-3 py-1 rounded">Remover</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Ações de Itens --}}
                <div class="flex items-center justify-between mb-4">
                    <button wire:click="addItem" class="bg-blue-700 text-white hover:text-black px-4 py-2 rounded">Adicionar Item +</button>
                    <button wire:click="refreshValues" class="bg-yellow-500 text-white hover:text-black px-4 py-2 rounded">↻</button>
                </div>

                {{-- Total e Salvar --}}
                <div class="flex items-center justify-between">
                    <button wire:click="save" class="bg-green-600 text-white hover:text-black px-6 py-2 rounded">Salvar Venda</button>
                    <div class="text-xl font-bold">Total: R$ {{ number_format($total, 2, ',', '.') }}</div>
                </div>

                {{-- Cancelar --}}
                <div class="text-right mt-6">
                    <button wire:click="$set('showCreateModal', false)" class="bg-gray-600 text-white hover:text-black px-6 py-2 rounded">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Histórico de Vendas --}}
    <h2 class="text-2xl font-semibold mt-10 mb-4">Histórico de Vendas</h2>
    <table class="w-full table-auto border border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Cliente</th>
                <th class="p-2 border text-center">Data</th>
                <th class="p-2 border text-center">Total</th>
                <th class="p-2 border text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td class="p-2 border">{{ $sale->client->name }}</td>
                    <td class="p-2 border text-center">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                    <td class="p-2 border text-center">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                    <td class="p-2 border text-center">
                        <div class="flex items-center gap-3 justify-center">
                            <button wire:click="viewDetails({{ $sale->id }})" class="bg-green-600 text-white hover:text-black px-3 py-1 rounded">Ver</button>
                            <button wire:click="edit({{ $sale->id }})" class="bg-blue-500 text-white hover:text-black px-3 py-1 rounded">Editar</button>
                            <button wire:click="delete({{ $sale->id }})" onclick="confirm('Tem certeza?') || event.stopImmediatePropagation()" class="bg-red-600 text-white hover:text-black px-3 py-1 rounded">Excluir</button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Popup Detalhes --}}
    @if ($viewingSale)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 backdrop-blur-sm"></div>

            <div class="relative bg-green-100 border-2 border-black rounded-lg shadow-lg p-6 z-10 max-w-2xl w-full">
                <h2 class="text-xl font-bold mb-4">Detalhes da Venda</h2>
                <table class="w-full border border-collapse text-left mb-4">
    <thead class="bg-green-200">
        <tr>
            <th class="p-2 border">Nome</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Número</th>
        </tr>
    </thead>
    <tbody class="bg-white">
        <tr>
            <td class="p-2 border">{{ $viewingSale->client->name }}</td>
            <td class="p-2 border">{{ $viewingSale->client->email }}</td>
            <td class="p-2 border">
                ({{ substr($viewingSale->client->number, 0, 2) }})
                {{ substr($viewingSale->client->number, 2, 5) }}-{{ substr($viewingSale->client->number, 7) }}
            </td>
        </tr>
    </tbody>
</table>
                <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($viewingSale->sale_date)->format('d/m/Y') }}</p>

                <table class="w-full table-auto border border-collapse mt-4">
                    <thead class="bg-green-200">
                        <tr>
                            <th class="p-2 border text-left">Produto</th>
                            <th class="p-2 border text-center">Quantidade</th>
                            <th class="p-2 border text-center">Valor Unitário</th>
                            <th class="p-2 border text-center">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($viewingSale->items as $item)
                            <tr class="bg-white">
                                <td class="p-2 border">{{ $item->product->name }}</td>
                                <td class="p-2 border text-center">{{ $item->quantity }}</td>
                                <td class="p-2 border text-center">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="p-2 border text-center">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-right font-bold text-lg mt-4">
                    Total: R$ {{ number_format($viewingSale->total, 2, ',', '.') }}
                </div>

                <div class="mt-4 text-right">
                    <button wire:click="closeDetails" class="bg-gray-500 text-white hover:bg-gray-600 px-4 py-2 rounded">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
