<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Usuarios\Index as UsuariosIndex;
use App\Livewire\Produtos\Index as ProdutosIndex;
use App\Livewire\Vendas\Index as VendasIndex;
use App\Livewire\Dashboard;
use App\Http\Controllers\DashboardController;

Route::get('/', Dashboard::class);


Route::get('/usuarios', UsuariosIndex::class)->name('usuarios.index');
Route::get('/produtos', ProdutosIndex::class)->name('produtos.index');
Route::get('/vendas', VendasIndex::class)->name('vendas.index');

Route::get('/api/chart-data/{ano}', [DashboardController::class, 'chartData']);

