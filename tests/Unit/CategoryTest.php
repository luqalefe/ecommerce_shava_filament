<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_products(): void
    {
        $category = Category::factory()->create();
        $product1 = Product::factory()->create(['category_id' => $category->id]);
        $product2 = Product::factory()->create(['category_id' => $category->id]);

        $this->assertCount(2, $category->products);
        $this->assertTrue($category->products->contains($product1));
        $this->assertTrue($category->products->contains($product2));
    }

    public function test_category_can_have_parent(): void
    {
        $parentCategory = Category::factory()->create();
        $childCategory = Category::factory()->create(['parent_id' => $parentCategory->id]);

        $this->assertInstanceOf(Category::class, $childCategory->parent);
        $this->assertEquals($parentCategory->id, $childCategory->parent->id);
    }

    public function test_category_can_have_children(): void
    {
        $parentCategory = Category::factory()->create();
        $child1 = Category::factory()->create(['parent_id' => $parentCategory->id]);
        $child2 = Category::factory()->create(['parent_id' => $parentCategory->id]);

        $this->assertCount(2, $parentCategory->children);
        $this->assertTrue($parentCategory->children->contains($child1));
        $this->assertTrue($parentCategory->children->contains($child2));
    }

    public function test_category_slug_is_generated(): void
    {
        $category = Category::factory()->create(['name' => 'Eletrônicos']);

        $this->assertNotNull($category->slug);
        $this->assertNotEmpty($category->slug);
    }
}

