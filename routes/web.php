<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Sales\Index as SalesIndex;
use App\Livewire\Dashboard;
use App\Http\Controllers\DashboardController;


Route::get('/', Dashboard::class);


Route::get('/usuarios', ClientsIndex::class)->name('usuarios.index');
Route::get('/produtos', ProductsIndex::class)->name('produtos.index');
Route::get('/vendas', SalesIndex::class)->name('vendas.index');

Route::get('/api/chart-data/{year}', [DashboardController::class, 'chartData']);

