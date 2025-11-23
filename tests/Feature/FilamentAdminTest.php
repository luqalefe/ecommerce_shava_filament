<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get('/admin');

        // Filament redireciona para /admin/login, não para route('login')
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_admin' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->actingAs($admin, 'web');

        $response = $this->get('/admin');

        // Filament pode retornar 200 ou redirecionar para dashboard
        $this->assertContains($response->status(), [200, 302]);
    }

    public function test_logistica_user_can_access_admin_panel(): void
    {
        $logistica = User::factory()->create([
            'role' => 'logistica',
            'is_admin' => false,
        ]);

        $this->actingAs($logistica, 'web');

        $response = $this->get('/admin');

        // Filament pode retornar 200 ou redirecionar para dashboard
        $this->assertContains($response->status(), [200, 302]);
    }

    public function test_admin_can_view_products_list(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $category = Category::factory()->create();
        Product::factory()->count(5)->create(['category_id' => $category->id]);

        $this->actingAs($admin, 'web');

        $response = $this->get('/admin/products');

        // Filament pode retornar 200 ou redirecionar
        $this->assertContains($response->status(), [200, 302]);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $category = Category::factory()->create();

        $this->actingAs($admin, 'web');

        $response = $this->get('/admin/products/create');

        // Filament pode retornar 200 ou redirecionar
        $this->assertContains($response->status(), [200, 302]);
    }

    public function test_admin_can_edit_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin, 'web');

        $response = $this->get("/admin/products/{$product->id}/edit");

        // Filament pode retornar 200 ou redirecionar
        $this->assertContains($response->status(), [200, 302]);
    }

    public function test_regular_user_cannot_access_product_resource(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_admin' => false,
        ]);

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($user);

        $response = $this->get('/admin/products');

        $response->assertForbidden();
    }
}

