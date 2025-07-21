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
    //public $password;
    public $number;


    public function loadUsuarios()
    {
        $this->usuarios = User::all();
    }

    public function mount()
    {
        $this->loadUsuarios();
    }

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
        //'password' => $this->userid ? ['nullable', 'min:4'] : ['min:4'],
    ]);

    $data = [
        'name' => $this->name,
        'email' => $this->email,
        'number' => $this->number,
    ];

    /*if ($this->password) {
        $data['password'] = Hash::make($this->password);
    }*/

    User::updateOrCreate(['id' => $this->userid], $data);

    session()->flash('message', $this->userid ? 'Usuário atualizado!' : 'Usuário criado!');
    $this->resetForm();
    $this->loadUsuarios();
}

    public function edit ($id)
    {
        $user = User::findOrFail($id);
        $this->userid = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->number = $user->number;

        $this->resetValidation();
    }

    public function delete($id)
    {
        User::findOrfail($id)->delete();
        session()->flash('message', 'Usuário deletado com sucesso');
        $this->usuarios = User::all();
    }

    public function resetForm()
    {
        $this->reset(['userid', 'name', 'email', 'number'/*'password'*/]);
    }

    public function render()
{
    return view('Livewire.Usuarios.Index')->layout('layouts.app', ['title' => 'Usuários']);
}
}
