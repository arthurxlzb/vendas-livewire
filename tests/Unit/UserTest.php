<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_relation_sales_and_fillable()
    {
        $user = User::factory()->create();
        Sale::factory()->create(['client_id' => $user->id]);

        $this->assertTrue($user->sales->first() instanceof Sale);
        $fillable = (new User())->getFillable();
        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
    }
}
