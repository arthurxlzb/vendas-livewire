<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Index extends Component
{
    public array $sales     = [];
    public array $clients   = [];
    public array $products  = [];

    public ?int   $saleId         = null;
    public ?int   $clientId       = null;
    public string $saleDate       = '';
    public array  $items          = [];
    public float  $total          = 0.0;
    public ?Sale  $viewingSale    = null;
    public bool   $showCreateModal = false;

    protected array $rules = [
        'clientId'           => 'required|exists:clients,id',
        'saleDate'           => 'required|date',
        'items'              => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity'   => 'required|integer|min:1',
    ];

    public function mount(): void
    {
        $this->clients  = Client::orderBy('name')->get()->toArray();
        $this->products = Product::orderBy('name')->get()->toArray();
        $this->loadSales();
        $this->resetForm();
    }

    public function loadSales(): void
    {
        $this->sales = Sale::with(['client', 'items.product'])
            ->orderByDesc('sale_date')
            ->get()
            ->toArray();
    }

    public function resetForm(): void
    {
        $this->saleId   = null;
        $this->clientId = null;
        $this->saleDate = Carbon::today()->toDateString();

        $this->items = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0.0, 'subtotal' => 0.0],
        ];

        $this->calculateTotal();
    }

    public function updated($property, $value): void
    {
        // Atualiza preço e subtotal ao mudar product_id ou quantity
        if (preg_match('/items\.(\d+)\.(product_id|quantity)/', $property, $m)) {
            $i = $m[1];
            $product = Product::find($this->items[$i]['product_id'] ?? null);
            $price   = $product->price ?? 0.0;
            $qty     = $this->items[$i]['quantity'] ?? 0;

            $this->items[$i]['unit_price'] = $price;
            $this->items[$i]['subtotal']   = $price * $qty;
            $this->calculateTotal();
        }
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0.0, 'subtotal' => 0.0];
        $this->calculateTotal();
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->total = collect($this->items)->sum('subtotal');
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'client_id' => $this->clientId,
                'sale_date' => $this->saleDate,
                'total'     => $this->total,
            ];

            if ($this->saleId) {
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

    public function edit(int $id): void
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

    public function delete(int $id): void
    {
        Sale::findOrFail($id)->delete();
        session()->flash('message', 'Venda excluída com sucesso!');
        $this->loadSales();
    }

    public function viewDetails(int $id): void
    {
        $this->viewingSale = Sale::with(['client', 'items.product'])->findOrFail($id);
    }

    public function closeDetails(): void
    {
        $this->viewingSale = null;
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function render()
    {
        return view('livewire.sales.index')
            ->layout('layouts.app', ['title' => 'Sales']);
    }
}
