<?php

namespace App\Livewire\Vendas;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Index extends Component
{
    public $sales;
    public $clients;
    public $products;

    public $saleId;
    public $clientId;
    public $saleDate;
    public $items = [];
    public $total = 0;
    public $viewingSale = null;
    public $showCreateModal = false;


    protected $rules = [
        'clientId'           => 'required|exists:users,id',
        'saleDate'           => 'required|date',
        'items'              => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity'   => 'required|integer|min:1',
    ];

    public function mount()
    {
        // Carrega os dados
        $this->clients  = User::orderBy('name')->get();
        $this->products = Product::orderBy('name')->get();
        $this->loadSales();
        $this->resetForm();
    }

    public function loadSales()
    {
        $this->sales = Sale::with('client')->orderByDesc('sale_date')->get();
    }

    // Limpa os campos do formulário e define valores padrão
    public function resetForm()
    {
        $this->saleId   = null;
        $this->clientId = null;
        $this->saleDate = Carbon::today()->toDateString();

        // Adiciona um item vazio por padrão
        $this->items = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'subtotal' => 0],
        ];

        $this->calculateTotal();
    }

    // Atualiza valores dinamicamente quando campos são alterados
    public function updated($propertyName, $value)
    {
        // Se o produto for alterado
        if (preg_match('/items\.(\d+)\.product_id/', $propertyName, $m)) {
            $i     = $m[1];
            $prod  = Product::find($value);
            $price = $prod ? $prod->price : 0;

            $this->items[$i]['unit_price'] = $price;
            $qty = $this->items[$i]['quantity'] ?? 0;
            $this->items[$i]['subtotal']  = $price * $qty;
        }

        // Se a quantidade for alterada
        if (preg_match('/items\.(\d+)\.quantity/', $propertyName, $m)) {
            $i     = $m[1];
            $price = $this->items[$i]['unit_price'] ?? 0;
            $this->items[$i]['subtotal']  = $price * $value;
        }

        $this->calculateTotal();
    }

    // Recalcula preços e subtotais de todos os itens
    public function refreshValues()
    {
        foreach ($this->items as $i => $item) {
            $prod  = Product::find($item['product_id']);
            $price = $prod?->price ?? 0;

            $this->items[$i]['unit_price'] = $price;
            $qty = $item['quantity'] ?? 0;
            $this->items[$i]['subtotal']  = $price * $qty;
        }

        $this->calculateTotal();
    }

    // Adiciona um novo item ao formulário de venda
    public function addItem()
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'subtotal' => 0];
        $this->calculateTotal();
    }

    // Remove um item da venda
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // reorganiza os índices
        $this->calculateTotal();
    }

    // Soma todos os subtotais dos itens
    public function calculateTotal()
    {
        $this->total = collect($this->items)->sum('subtotal');
    }


    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'client_id' => $this->clientId,
                'sale_date' => $this->saleDate,
                'total'     => $this->total,
            ];

            if ($this->saleId) {
                // Atualiza a venda existente
                $sale = Sale::findOrFail($this->saleId);
                $sale->update($data);
                $sale->items()->delete();
            } else {

                $sale = Sale::create($data);
            }

            foreach ($this->items as $item) {
                $sale->items()->create($item);
            }
        });

        session()->flash('message', $this->saleId ? 'Venda atualizada!' : 'Venda criada!');
        $this->loadSales();
        $this->resetForm();
        $this->showCreateModal = false;
    }

    // Carrega os dados de uma venda existente no formulário de edição
    public function edit($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        $this->saleId   = $sale->id;
        $this->clientId = $sale->client_id;
        $this->saleDate = $sale->sale_date->toDateString();
        $this->items    = $sale->items->map(fn($i) => [
            'product_id' => $i->product_id,
            'quantity'   => $i->quantity,
            'unit_price' => $i->unit_price,
            'subtotal'   => $i->subtotal,
        ])->toArray();

        $this->resetValidation();
        $this->calculateTotal();
        $this->showCreateModal = true;
    }

    public function delete($id)
    {
        Sale::findOrFail($id)->delete();
        session()->flash('message', 'Venda deletada com sucesso');
        $this->loadSales();
    }

    public function viewDetails($id)
    {
        $this->viewingSale = Sale::with(['client', 'items.product'])->findOrFail($id);
    }

    public function closeDetails()
    {
        $this->viewingSale = null;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function render()
    {
        return view('Livewire.vendas.index')->layout('layouts.app', ['title' => 'Vendas']);
    }
}
