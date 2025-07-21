<div class="p-6 max-w-5xl mx-auto">
    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between mt-5">
        <h2 class="text-4xl font-bold">Produtos</h2>
        <button wire:click="openCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white hover:text-black px-5 py-3 rounded">
            Cadastrar Produto
        </button>
    </div>

    {{-- Mensagem de sucesso --}}
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Modal de Criação/Edição --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 backdrop-blur-sm"></div>

            <div class="relative bg-blue-200 border-2 border-black rounded-lg shadow-lg p-6 z-10 max-w-3xl w-full">
                <h2 class="text-xl font-bold mb-4">{{ $productId ? 'Editar Produto' : 'Cadastrar Produto' }}</h2>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Nome</label>
                            <input type="text" wire:model.defer="name" class="w-full p-2 border rounded" />
                            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Preço (R$)</label>
                            <input type="number" step="0.01" wire:model.defer="price" class="w-full p-2 border rounded" />
                            @error('price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Unidade</label>
                            <select wire:model="unidade" class="w-full p-2 border rounded">
                                <option value="">Selecione...</option>
                                @foreach($unitOptions as $unit)
                                    <option value="{{ $unit }}">{{ strtoupper($unit) }}</option>
                                @endforeach
                            </select>
                            @error('unidade') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Quantidade</label>
                            <input type="number" step="0.01" wire:model.defer="quantidade" class="w-full p-2 border rounded" />
                            @error('quantidade') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Descrição</label>
                        <input type="text" wire:model.defer="description" class="w-full p-2 border rounded" />
                        @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-between mt-4">
                        <button type="submit" class="bg-green-600 text-white hover:text-black px-4 py-2 rounded">
                            {{ $productId ? 'Atualizar' : 'Salvar' }}
                        </button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="bg-gray-600 text-white hover:text-black px-4 py-2 rounded">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Lista de Produtos --}}
    <h2 class="text-2xl font-semibold mt-10 mb-4">Lista de Produtos</h2>
    <table class="w-full border border-collapse">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Nome</th>
                <th class="p-2 border">Preço</th>
                <th class="p-2 border">Unidade</th>
                <th class="p-2 border">Qtd</th>
                <th class="p-2 border">Descrição</th>
                <th class="p-2 border text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td class="p-2 border">{{ $p->id }}</td>
                    <td class="p-2 border">{{ $p->name }}</td>
                    <td class="p-2 border">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
                    <td class="p-2 border">{{ $p->unidade }}</td>
                    <td class="p-2 border">{{ number_format($p->quantidade) }}</td>
                    <td class="p-2 border">{{ $p->description }}</td>
                    <td class="p-2 border text-center">
                        <div class="flex items-center gap-2 justify-center">
                            <button wire:click="edit({{ $p->id }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:text-black">Editar</button>
                            <button wire:click="delete({{ $p->id }})" onclick="confirm('Tem certeza que deseja excluir?') || event.stopImmediatePropagation()" class="bg-red-700 text-white px-3 py-1 rounded hover:text-black">Excluir</button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
