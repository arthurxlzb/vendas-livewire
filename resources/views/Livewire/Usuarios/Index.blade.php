<div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Clientes</h1>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Formulário de criar/editar -->
    <form wire:submit.prevent="save" class="space-y-4 bg-gray-100 p-4 rounded mb-6">
        <div class="flex gap-4">
            <div class="w-1/3">
                <label class="block text-sm font-medium">Nome</label>
                <input type="text" wire:model.defer="name" class="w-full p-2 border rounded" />
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="w-1/3">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" wire:model.defer="email" class="w-full p-2 border rounded" />
                @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="w-1/3">
                <label class="block text-sm font-medium">Número</label>
                <input type="text" wire:model.defer="number" class="w-full p-2 border rounded" />
                @error('number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white hover:text-black px-4 py-2 rounded">
                {{ $userid ? 'Atualizar' : 'Cadastrar' }}
            </button>

            @if($userid)
                <button type="button" wire:click="resetForm" class="ml-4 text-gray-600 underline">
                    Cancelar Edição
                </button>
            @endif
        </div>
    </form>

    <!-- Tabela de usuários -->
    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                <th class="text-left p-2">ID</th>
                <th class="text-left p-2">Nome</th>
                <th class="text-left p-2">Email</th>
                <th class="text-left p-2">Número</th>
                <th class="text-left p-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr class="border-t">
                    <td class="p-2">{{ $usuario->id }}</td>
                    <td class="p-2">{{ $usuario->name }}</td>
                    <td class="p-2">{{ $usuario->email }}</td>
                    <td class="p-2">{{ $usuario->number }}</td>
                    <td class="p-2 flex items-center gap-4">
                        <button wire:click="edit({{ $usuario->id }})" class="bg-blue-500 text-white hover:text-black px-3 py-1 rounded">
                            Editar
                        </button>
                        <button
                            wire:click="delete({{ $usuario->id }})"
                            class="bg-red-800 text-white hover:text-black px-3 py-1 rounded"
                            onclick="confirm('Tem certeza que deseja excluir este usuário?') || event.stopImmediatePropagation()">
                            Excluir
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
