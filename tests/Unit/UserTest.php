<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Order;
use App\Models\Endereco;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_orders(): void
    {
        $user = User::factory()->create();
        $order1 = Order::factory()->create(['user_id' => $user->id]);
        $order2 = Order::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->orders);
        $this->assertTrue($user->orders->contains($order1));
        $this->assertTrue($user->orders->contains($order2));
    }

    public function test_user_has_many_enderecos(): void
    {
        $user = User::factory()->create();
        $endereco1 = Endereco::factory()->create(['user_id' => $user->id]);
        $endereco2 = Endereco::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->enderecos);
        $this->assertTrue($user->enderecos->contains($endereco1));
        $this->assertTrue($user->enderecos->contains($endereco2));
    }

    public function test_user_is_admin_returns_true_when_role_is_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_user_is_admin_returns_true_when_is_admin_is_true(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->assertTrue($user->isAdmin());
    }

    public function test_user_is_admin_returns_false_for_regular_user(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_admin' => false]);

        $this->assertFalse($user->isAdmin());
    }

    public function test_user_is_logistica_returns_true_when_role_is_logistica(): void
    {
        $user = User::factory()->create(['role' => 'logistica']);

        $this->assertTrue($user->isLogistica());
    }

    public function test_user_can_access_admin_when_is_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->canAccessAdmin());
    }

    public function test_user_can_access_admin_when_is_logistica(): void
    {
        $user = User::factory()->create(['role' => 'logistica']);

        $this->assertTrue($user->canAccessAdmin());
    }

    public function test_user_cannot_access_admin_when_is_regular_user(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_admin' => false]);

        $this->assertFalse($user->canAccessAdmin());
    }
}

