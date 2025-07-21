<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
   <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@700&display=swap" rel="stylesheet" />

    <title>{{ $title ?? 'Sistema de Vendas' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

    {{-- Navbar fixa no topo --}}
    <nav class="bg-white fixed w-full top-0 left-0 z-50 shadow">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            {{-- Logo no canto esquerdo --}}
            <div class="flex items-center space-x-2">
                <a href="{{ url('/') }}" class="text-blue-500 font-bold text-xl">
                    <span class="text-3xl font-extrabold text-blue-600 hover:bg-blue-300" style="font-family: 'Raleway', sans-serif;">AB</span>


                </a>
                 <span class="text-lg font-semibold text-gray-700">Sistema de Vendas</span>
            </div>

            {{-- Links no canto direito --}}
            <div class="flex space-x-4">
                <a href="{{ route('usuarios.index') }}"
                   class="text-blue-500 px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-300
                   {{ request()->routeIs('usuarios.*') ? 'bg-blue-200 font-bold' : '' }}">
                    Clientes
                </a>
                <a href="{{ route('produtos.index') }}"
                   class="text-blue-500 px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-300
                   {{ request()->routeIs('produtos.*') ? 'bg-blue-200 font-bold' : '' }}">
                    Produtos
                </a>
                <a href="{{ route('vendas.index') }}"
                    class="text-blue-500 px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-300
                    {{ request()->routeIs('vendas.*') ? 'bg-blue-200 font-bold' : '' }}">
                    Vendas
                </a>
            </div>
        </div>
    </div>
</nav>


    {{-- Espaço para compensar a navbar fixa --}}
    <div class="pt-16 max-w-7xl mx-auto px-4">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
