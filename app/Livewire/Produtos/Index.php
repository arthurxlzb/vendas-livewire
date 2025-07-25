<?php

namespace App\Livewire\Produtos;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Validation\Rule;

class Index extends Component
{

    public $products = [];


    public $productId;
    public $name;
    public $price;
    public $description;
    public $quantidade;
    public $unidade;

    public $showCreateModal = false;

    //unidades
    public $unitOptions = ['G', 'KG', 'TON', 'ML', 'L', 'M²', 'M³', 'CM', 'M', 'KM', 'UNI'];

    protected function rules()
    {
        return [
            'name'        => ['required', 'min:3', 'max:100'],
            'price'       => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantidade'  => ['required', 'numeric', 'min:0.01'],
            'unidade'     => ['required', Rule::in($this->unitOptions)],
        ];
    }

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $this->products = Product::orderBy('name')->get();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function save()
    {
        $data = $this->validate();

        Product::updateOrCreate(
            ['id' => $this->productId], // Se tiver ID, atualiza; senão, cria novo
            $data
        );

        session()->flash('message', $this->productId ? 'Produto atualizado!' : 'Produto criado!');

        $this->showCreateModal = false;
        $this->resetForm();
        $this->loadProducts();
    }

    // Preenche o formulário com dados do produto selecionado
    public function edit($id)
    {
        $p = Product::findOrFail($id);

        $this->productId   = $p->id;
        $this->name        = $p->name;
        $this->price       = $p->price;
        $this->description = $p->description;
        $this->quantidade  = $p->quantidade;
        $this->unidade     = $p->unidade;

        $this->resetValidation();
        $this->showCreateModal = true;
    }


    public function delete($id)
    {
        Product::findOrFail($id)->delete();
        session()->flash('message', 'Produto deletado com sucesso');
        $this->loadProducts();
    }


    public function resetForm()
    {
        $this->reset(['productId', 'name', 'price', 'description', 'quantidade', 'unidade']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('Livewire.Produtos.Index')
            ->layout('layouts.app', ['title' => 'Produtos']);
    }
}
