<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsuariosIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount_and_loadUsuarios()
    {
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);

        Livewire::test('usuarios.index')
            ->assertSet('usuarios.0.name', 'Bob')  // orderBy desc
            ->assertSet('usuarios.1.name', 'Alice');
    }

    public function test_open_and_close_modal_resets_form()
    {
        $component = Livewire::test('usuarios.index');

        // Simulate filling the form
        $component->set('name', 'Charlie')
                  ->set('email', 'charlie@example.com')
                  ->set('number', '12345678901');

        // Open modal should reset form fields and showModal true
        $component->call('openModal')
                  ->assertSet('showModal', true)
                  ->assertSet('name', null)
                  ->assertSet('email', null)
                  ->assertSet('number', null);

        // Close modal should hide it
        $component->call('closeModal')
                  ->assertSet('showModal', false);
    }

    public function test_can_create_and_update_user()
    {
        // Create
        Livewire::test('usuarios.index')
            ->set('name', 'Daniel')
            ->set('email', 'daniel@example.com')
            ->set('number', '09876543210')
            ->call('save');

        $this->assertDatabaseHas('users', ['name' => 'Daniel', 'email' => 'daniel@example.com']);

        $user = User::first();

        // Update
        Livewire::test('usuarios.index')
            ->call('edit', $user->id)
            ->set('name', 'Daniel Silva')
            ->call('save');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Daniel Silva']);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        Livewire::test('usuarios.index')
            ->call('delete', $user->id);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
