<?php

namespace App\Livewire\Produtos;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Validation\Rule;

class Index extends Component
{
    // Lista de produtos
    public $products = [];

    // Formulário
    public $productId;
    public $name;
    public $price;
    public $description;
    public $quantidade;
    public $unidade;

    // Controle do modal
    public $showCreateModal = false;

    // Opções de unidade
    public $unitOptions = ['G', 'KG', 'TON', 'ML', 'L', 'M²', 'M³', 'CM', 'M', 'KM', 'UNI'];

    protected function rules()
    {
        return [
            'name'        => ['required','min:3','max:100'],
            'price'       => ['required','numeric','min:0.01'],
            'description' => ['nullable','string','max:1000'],
            'quantidade'  => ['required','numeric','min:0.01'],
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

    /**
     * Abre o modal em modo criação.
     */
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    /**
     * Salva ou atualiza o produto, fecha modal e recarrega lista.
     */
    public function save()
    {
        $data = $this->validate();

        Product::updateOrCreate(
            ['id' => $this->productId],
            $data
        );

        session()->flash('message', $this->productId ? 'Produto atualizado!' : 'Produto criado!');

        $this->showCreateModal = false;
        $this->resetForm();
        $this->loadProducts();
    }

    /**
     * Carrega o produto no formulário e abre o modal em modo edição.
     */
    public function edit($id)
    {
        $p = Product::findOrFail($id);
        $this->productId   = $p->id;
        $this->name        = $p->name;
        $this->price       = $p->price;
        $this->description = $p->description;
        $this->quantidade  = $p->quantidade;
        $this->unidade     = $p->unidade;

        $this->showCreateModal = true;
    }

    /**
     * Exclui o produto e recarrega a lista.
     */
    public function delete($id)
    {
        Product::findOrFail($id)->delete();
        session()->flash('message', 'Produto deletado com sucesso');
        $this->loadProducts();
    }

    /**
     * Reseta o formulário para valores iniciais.
     */
    public function resetForm()
    {
        $this->reset(['productId','name','price','description','quantidade','unidade']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('Livewire.Produtos.Index')
            ->layout('layouts.app', ['title' => 'Produtos']);
    }
}
