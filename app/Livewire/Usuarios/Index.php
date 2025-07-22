<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Index extends Component
{
    public $usuarios;
    public $userid;
    public $name;
    public $email;
    public $number;
    public $showModal = false;

    public function mount()
    {
        $this->loadUsuarios();
    }

    public function loadUsuarios()
    {
        $this->usuarios = User::orderBy('id', 'desc')->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    // Cria ou atualiza um usuário no banco
    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                'min:3',
                Rule::unique('users', 'name')->ignore($this->userid)
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userid)
            ],
            'number' => [
                'nullable',
                'size:11',
                'regex:/^[0-9]{11}$/',
                Rule::unique('users', 'number')->ignore($this->userid)
            ],
        ]);

        $data = [
            'name'   => $this->name,
            'email'  => $this->email,
            'number' => $this->number,
        ];

        // Se tiver um ID, atualiza o usuário. Senão, cria um novo.
        User::updateOrCreate(['id' => $this->userid], $data);

        session()->flash('message', $this->userid ? 'Usuário atualizado!' : 'Usuário criado!');
        $this->closeModal();
        $this->loadUsuarios();
    }

    // Carrega os dados de um usuário no formulário para edição
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userid = $user->id;
        $this->name   = $user->name;
        $this->email  = $user->email;
        $this->number = $user->number;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Usuário deletado com sucesso');
        $this->loadUsuarios();
    }

    public function resetForm()
    {
        $this->reset(['userid', 'name', 'email', 'number']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('Livewire.Usuarios.Index')->layout('layouts.app', ['title' => 'Usuários']);
    }
}
