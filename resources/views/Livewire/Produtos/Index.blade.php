<div class="p-6 max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Produtos</h1>

  @if (session()->has('message'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
      {{ session('message') }}
    </div>
  @endif

  <form wire:submit.prevent="save" class="space-y-4 bg-gray-100 p-4 rounded mb-6">
    <div class="flex gap-4">
      <div class="w-1/4">
        <label class="block text-sm font-medium">Nome</label>
        <input type="text" wire:model.defer="name" class="w-full p-2 border rounded" />
        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
      </div>

      <div class="w-1/4">
        <label class="block text-sm font-medium">Preço (R$)</label>
        <input type="number" step="0.01" wire:model.defer="price" class="w-full p-2 border rounded" />
        @error('price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
      </div>

      <div class="w-1/4">
        <label class="block text-sm font-medium">Unidade</label>
        <select wire:model="unidade" class="w-full p-2 border rounded">
         <option value="">Selecione...</option>
            @foreach($unitOptions as $unit)
             <option value="{{ $unit }}">{{ strtoupper($unit) }}</option>
         @endforeach
         </select>
         @error('unidade') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

    <div class="w-1/4">
        <label class="block text-sm font-medium">Quantidade</label>
        <input type="number" step="0.01" wire:model="quantidade" class="w-full p-2 border rounded" />
        @error('quantidade') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
         </div>

        </div>

    <div class="flex gap-4">
      <div class="w-full">
        <label class="block text-sm font-medium">Descrição</label>
        <input type="text" wire:model="description" class="w-full p-2 border rounded" />
        @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
      </div>
    </div>

    <div class="flex items-end gap-4">
      <button type="submit" class="bg-blue-600 text-white hover:text-black px-4 py-2 rounded">
        {{ $productId ? 'Atualizar' : 'Cadastrar' }}
      </button>
      @if($productId)
        <button type="button" wire:click="resetForm" class="text-gray-600 underline">
          Cancelar
        </button>
      @endif
    </div>
  </form>

  <table class="w-full border">
    <thead class="bg-gray-200">
      <tr>
        <th class="text-left p-2">ID</th>
        <th class="text-left p-2">Nome</th>
        <th class="text-left p-2">Preço</th>
        <th class="text-left p-2">Quantidade</th>
        <th class="text-left p-2">Unidade</th>
        <th class="text-left p-2">Descrição</th>
        <th class="text-left p-2">Ações</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($products as $p)
        <tr class="border-t">
          <td class="p-2">{{ $p->id }}</td>
          <td class="p-2">{{ $p->name }}</td>
          <td class="p-2">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
          <td class="p-2">{{ number_format($p->quantidade, 2, ',', '.') }}</td>
          <td class="p-2">{{ $p->unidade }}</td>
          <td class="p-2">{{ $p->description }}</td>
          <td class="p-2 flex items-center gap-4">
            <button wire:click="edit({{ $p->id }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:text-black">
              Editar
            </button>
            <button wire:click="delete({{ $p->id }})"
              class="bg-red-800 text-white px-3 py-1 rounded hover:text-black"
              onclick="confirm('Tem certeza que deseja excluir este produto?') || event.stopImmediatePropagation()">
              Excluir
            </button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
