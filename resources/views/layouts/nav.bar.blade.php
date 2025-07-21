<nav class="bg-white px-4 py-6 text-blue-500 flex gap-4">
    <a href="{{ route('usuarios.index') }}"
       class="px-3 py-2 rounded hover:bg-blue-700 {{ request()->routeIs('usuarios.*') ? 'bg-gray-900 font-bold' : '' }}">
       Clientes
    </a>
    <a href="{{ route('produtos.index') }}"
       class="px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('produtos.*') ? 'bg-gray-900 font-bold' : '' }}">
       Produtos
    </a>
    <a href="{{ route('vendas.index') }}"
   class="text-blue-500 px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-300
   {{ request()->routeIs('vendas.*') ? 'bg-blue-200 font-bold' : '' }}">
    Vendas
</a>
</nav>
