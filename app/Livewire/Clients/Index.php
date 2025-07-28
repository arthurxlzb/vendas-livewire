<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use App\Models\Client;
use Illuminate\Validation\Rule;

class Index extends Component
{
    public array $clients = [];

    public ?int   $clientId    = null;
    public string $name        = '';
    public string $email       = '';
    public ?string $phone      = null;

    public bool   $showModal   = false;

    protected array $rules = [
    'name'  => 'required|string|min:3|max:100',
    'email' => 'required|email',
    'phone' => 'nullable|digits:11|regex:/^[0-9]{11}$/',
];


    public function mount(): void
    {
        $this->loadClients();
    }

    public function loadClients(): void
    {
       $this->clients = Client::withCount('sales')
    ->orderBy('id', 'desc')
    ->get()
    ->toArray();

    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function save(): void
{
    $rules = $this->rules;


    $rules['email'] = 'required|email|unique:clients,email,' . ($this->clientId ?? 'NULL') . ',id';
    $rules['phone'] = 'nullable|digits:11|regex:/^[0-9]{11}$/|unique:clients,phone,' . ($this->clientId ?? 'NULL') . ',id';

    $data = $this->validate($rules);

    Client::updateOrCreate(
        ['id' => $this->clientId],
        $data
    );

    session()->flash('message', $this->clientId ? 'Client updated!' : 'Client created!');
    $this->closeModal();
    $this->loadClients();
}


    public function edit(int $id): void
    {
        $client = Client::findOrFail($id);

        $this->clientId = $client->id;
        $this->name     = $client->name;
        $this->email    = $client->email;
        $this->phone    = $client->phone;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function delete(int $id): void
    {
        $client = Client::findOrFail($id);

        if ($client->sales()->exists()) {
            $this->addError('cannotDelete', 'Esse cliente tem vendas associadas e nao pode ser excluído.');
            return;
        }

        $client->delete();

        session()->flash('message', 'Cliente excluído com sucesso!');
        $this->loadClients();
    }

    public function resetForm(): void
    {
        $this->reset(['clientId', 'name', 'email', 'phone']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.clients.index')
            ->layout('layouts.app', ['title' => 'Clients']);
    }
}
