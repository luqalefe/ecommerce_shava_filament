<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\AttributeValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_has_many_images(): void
    {
        $product = Product::factory()->create();
        $image1 = ProductImage::factory()->create(['product_id' => $product->id]);
        $image2 = ProductImage::factory()->create(['product_id' => $product->id]);

        $this->assertCount(2, $product->images);
        $this->assertTrue($product->images->contains($image1));
        $this->assertTrue($product->images->contains($image2));
    }

    public function test_product_has_many_reviews(): void
    {
        $product = Product::factory()->create();
        $review1 = Review::factory()->create(['product_id' => $product->id]);
        $review2 = Review::factory()->create(['product_id' => $product->id]);

        $this->assertCount(2, $product->reviews);
        $this->assertTrue($product->reviews->contains($review1));
        $this->assertTrue($product->reviews->contains($review2));
    }

    public function test_product_belongs_to_many_attribute_values(): void
    {
        $product = Product::factory()->create();
        $attributeValue1 = AttributeValue::factory()->create();
        $attributeValue2 = AttributeValue::factory()->create();

        $product->attributeValues()->attach([$attributeValue1->id, $attributeValue2->id]);

        $this->assertCount(2, $product->attributeValues);
        $this->assertTrue($product->attributeValues->contains($attributeValue1));
        $this->assertTrue($product->attributeValues->contains($attributeValue2));
    }

    public function test_product_price_is_casted_to_decimal(): void
    {
        $product = Product::factory()->create(['price' => 99.99]);

        // Laravel retorna decimal como string, mas podemos validar o valor
        $this->assertEquals('99.99', (string) $product->price);
        $this->assertEquals(99.99, (float) $product->price);
    }

    public function test_product_is_active_is_casted_to_boolean(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->assertIsBool($product->is_active);
        $this->assertTrue($product->is_active);
    }

    public function test_product_can_have_sale_price(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'sale_price' => 79.99,
        ]);

        $this->assertEquals(100.00, $product->price);
        $this->assertEquals(79.99, $product->sale_price);
    }
}

