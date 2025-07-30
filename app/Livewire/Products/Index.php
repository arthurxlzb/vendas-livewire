<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Product;
// use Illuminate\Database\Eloquent\Relations\HasMany;

class Index extends Component
{
    public array $products = [];

    public ?int $productId = null;
    public string $name = '';
    public float $price = 0.0;
    public string $description = '';
    public float $quantity = 0.0;
    public string $unit = '';

    public bool $showCreateModal = false;

    public array $unitOptions = ['G', 'KG', 'TON', 'ML', 'L', 'M²', 'M³', 'CM', 'M', 'KM', 'UNI'];

    /**
     * Regras de validação para produto.
     *
     * @var array<string,string>
     */
    protected array $rules = [
        'name'        => 'required|string|min:3|max:100',
        'price'       => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:1000',
        'quantity'    => 'required|integer|min:1',
        'unit'        => 'required|in:G,KG,TON,ML,L,M²,M³,CM,M,KM,UNI',
    ];

    public function mount(): void
    {
        $this->loadProducts();
    }

    public function loadProducts(): void
    {
        $this->products = Product::withCount('saleItems')->orderBy('name')->get()->toArray();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function save(): void
    {
        $this->validate();

        Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'name'        => $this->name,
                'price'       => $this->price,
                'description' => $this->description,
                'quantity'    => $this->quantity,
                'unit'        => $this->unit,
            ]
        );

        session()->flash('message', $this->productId ? 'Product updated!' : 'Product created!');

        $this->showCreateModal = false;
        $this->resetForm();
        $this->loadProducts();
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->productId   = $product->id;
        $this->name        = $product->name;
        $this->price       = $product->price;
        $this->description = $product->description ?? '';
        $this->quantity    = $product->quantity;
        $this->unit        = $product->unit;

        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        if ($product->saleItems()->exists()) {
            $this->addError('cannotDelete', 'This product has associated sales and cannot be deleted.');
            return;
        }

        $product->delete();

        session()->flash('message', 'Product deleted successfully');
        $this->loadProducts();
    }

    public function resetForm(): void
    {
        $this->reset(['productId', 'name', 'price', 'description', 'quantity', 'unit']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.products.index')
            ->layout('layouts.app', ['title' => 'Products']);
    }
}
