<div class="p-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mt-5">
        <h2 class="text-4xl font-bold">👤 Clientes</h2>
        <button wire:click="openModal" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-3 rounded hover:text-black">
            Novo Cliente+
        </button>
    </div>

    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 backdrop-blur-sm"></div>
            <div class="relative bg-white border border-gray-300 rounded-lg shadow-lg p-6 z-10 max-w-3xl w-full">
                <h2 class="text-xl font-bold mb-4">{{ $clientId ? 'Editar Cliente' : 'Criar Cliente' }}</h2>
                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Nome</label>
                            <input type="text" wire:model.defer="name" class="w-full p-2 border rounded" />
                            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Email</label>
                            <input type="email" wire:model.defer="email" class="w-full p-2 border rounded" />
                            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Telefone</label>
                            <input type="text" wire:model.defer="phone" class="w-full p-2 border rounded" />
                            @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:text-black hover:bg-green-700">
                            {{ $clientId ? 'Atualizar' : 'Salvar' }}
                        </button>
                        <button type="button" wire:click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded hover:text-black hover:bg-gray-700">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Clients List --}}
    <h2 class="text-2xl font-semibold mt-10 mb-4">Lista de Clientes</h2>
    <table class="w-full border border-collapse">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Nome</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Telefone</th>
                <th class="p-2 border text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $c)
                <tr>
                    <td class="p-2 border">{{ $c['id'] }}</td>
                    <td class="p-2 border">{{ $c['name'] }}</td>
                    <td class="p-2 border">{{ $c['email'] }}</td>
                    <td class="p-2 border whitespace-nowrap">
                        ({{ substr($c['phone'], 0, 2) }}) {{ substr($c['phone'], 2, 5) }}-{{ substr($c['phone'], 7, 4) }}
                    </td>
                    <td class="p-2 border text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button wire:click="edit({{ $c['id'] }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700">📝</button>
                            <button
                        wire:click="delete({{ $c['id'] }})"
                         onclick="confirm('Tem certeza que deseja excluir?') || event.stopImmediatePropagation()"
                            @if(($c['sales_count'] ?? 0) > 0)
                            disabled
                            class="bg-gray-400 text-white px-3 py-1 rounded cursor-not-allowed"
                              @else
                              class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-800"
                                  @endif
                                 >
                                    🗑️
                                         </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
