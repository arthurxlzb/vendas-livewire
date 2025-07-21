<div class="p-6 max-w-5xl mx-auto">
    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between mt-5">
        <h2 class="text-4xl font-bold">👤 Usuários</h2>
        <button wire:click="openModal" class="bg-blue-500 hover:bg-blue-600 text-white hover:text-black px-5 py-3 rounded">
            Novo Usuário+
        </button>
    </div>

    {{-- Mensagem de sucesso --}}
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Modal de Criação/Edição --}}
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 backdrop-blur-sm"></div>

            <div class="relative bg-blue-200 border-2 border-black rounded-lg shadow-lg p-6 z-10 max-w-3xl w-full">
                <h2 class="text-xl font-bold mb-4">{{ $userid ? 'Editar Cliente' : 'Cadastrar Cliente' }}</h2>

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
                            <label class="block text-sm font-medium">Número</label>
                            <input type="text" wire:model.defer="number" class="w-full p-2 border rounded" />
                            @error('number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-4">
                        <button type="submit" class="bg-green-600 text-white hover:text-black px-4 py-2 rounded">
                            {{ $userid ? 'Atualizar' : 'Salvar' }}
                        </button>
                        <button type="button" wire:click="closeModal" class="bg-gray-600 text-white hover:text-black px-4 py-2 rounded">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Lista de Usuários --}}
    <h2 class="text-2xl font-semibold mt-10 mb-4">Lista de Usuários</h2>
    <table class="w-full border border-collapse">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Nome</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Número</th>
                <th class="p-2 border text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td class="p-2 border">{{ $usuario->id }}</td>
                    <td class="p-2 border">{{ $usuario->name }}</td>
                    <td class="p-2 border">{{ $usuario->email }}</td>
                    <td class="p-2 border whitespace-nowrap">
                        ({{ substr($usuario->number, 0, 2) }}) {{ substr($usuario->number, 2, 5) }}-{{ substr($usuario->number, 7) }}
                    </td>

                    <td class="p-2 border text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button wire:click="edit({{ $usuario->id }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:text-black">Editar</button>
                            <button wire:click="delete({{ $usuario->id }})"
                                onclick="confirm('Tem certeza que deseja excluir?') || event.stopImmediatePropagation()"
                                class="bg-red-700 text-white px-3 py-1 rounded hover:text-black">
                                Excluir
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
