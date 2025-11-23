<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Models\Endereco;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    public function test_order_belongs_to_endereco(): void
    {
        $user = User::factory()->create();
        $endereco = Endereco::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'endereco_id' => $endereco->id,
        ]);

        $this->assertInstanceOf(Endereco::class, $order->endereco);
        $this->assertEquals($endereco->id, $order->endereco->id);
    }

    public function test_order_has_many_items(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $item1 = OrderItem::factory()->create(['order_id' => $order->id]);
        $item2 = OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertCount(2, $order->items);
        $this->assertTrue($order->items->contains($item1));
        $this->assertTrue($order->items->contains($item2));
    }

    public function test_order_can_have_different_statuses(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $order->status);

        $order->update(['status' => 'processing']);
        $this->assertEquals('processing', $order->status);
    }

    public function test_order_can_have_payment_method(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'mercadopago',
        ]);

        $this->assertEquals('mercadopago', $order->payment_method);
    }

    public function test_order_total_amount_is_stored(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 150.50,
            'shipping_cost' => 10.00,
        ]);

        $this->assertEquals(150.50, $order->total_amount);
        $this->assertEquals(10.00, $order->shipping_cost);
    }
}

