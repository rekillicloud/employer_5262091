<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function createProducts(): void
    {
        $category = Category::create(['name' => 'Electronics']);
        $category2 = Category::create(['name' => 'Books']);

        Product::factory()->create([
            'name' => 'iPhone 15',
            'price' => 999.99,
            'category_id' => $category->id,
            'in_stock' => true,
            'rating' => 4.8,
        ]);

        Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'price' => 799.99,
            'category_id' => $category->id,
            'in_stock' => true,
            'rating' => 4.5,
        ]);

        Product::factory()->create([
            'name' => 'MacBook Pro',
            'price' => 1999.99,
            'category_id' => $category->id,
            'in_stock' => false,
            'rating' => 4.9,
        ]);

        Product::factory()->create([
            'name' => 'Clean Code',
            'price' => 29.99,
            'category_id' => $category2->id,
            'in_stock' => true,
            'rating' => 4.7,
        ]);

        Product::factory()->create([
            'name' => 'Design Patterns',
            'price' => 49.99,
            'category_id' => $category2->id,
            'in_stock' => false,
            'rating' => 4.3,
        ]);
    }

    public function test_index_returns_200(): void
    {
        $this->createProducts();
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_index_returns_paginated_products(): void
    {
        $this->createProducts(20);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }

    public function test_search_by_name(): void
    {
        $this->createProducts();
        $response = $this->get('/?q=iPhone');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15');
        $response->assertDontSee('Samsung Galaxy');
    }

    public function test_search_by_partial_name(): void
    {
        $this->createProducts();
        $response = $this->get('/?q=Galaxy');
        $response->assertStatus(200);
        $response->assertSee('Samsung Galaxy');
        $response->assertDontSee('iPhone 15');
    }

    public function test_filter_by_price_from(): void
    {
        $this->createProducts();
        $response = $this->get('/?price_from=500');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15');
        $response->assertSee('Samsung Galaxy');
        $response->assertSee('MacBook Pro');
        $response->assertDontSee('Clean Code');
        $response->assertDontSee('Design Patterns');
    }

    public function test_filter_by_price_to(): void
    {
        $this->createProducts();
        $response = $this->get('/?price_to=50');
        $response->assertStatus(200);
        $response->assertSee('Clean Code');
        $response->assertSee('Design Patterns');
        $response->assertDontSee('iPhone 15');
        $response->assertDontSee('MacBook Pro');
    }

    public function test_filter_by_price_range(): void
    {
        $this->createProducts();
        $response = $this->get('/?price_from=500&price_to=1000');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15');
        $response->assertSee('Samsung Galaxy');
        $response->assertDontSee('MacBook Pro');
        $response->assertDontSee('Clean Code');
    }

    public function test_filter_by_category_id(): void
    {
        $this->createProducts();
        $category = Category::first();
        $response = $this->get('/?category_id=' . $category->id);
        $response->assertStatus(200);
        $response->assertSee('iPhone 15');
        $response->assertDontSee('Clean Code');
    }

    public function test_filter_by_in_stock_true(): void
    {
        $this->createProducts();
        $response = $this->get('/?in_stock=1');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15');
        $response->assertSee('Clean Code');
        $response->assertDontSee('MacBook Pro');
        $response->assertDontSee('Design Patterns');
    }

    public function test_filter_by_in_stock_false(): void
    {
        $this->createProducts();
        $response = $this->get('/?in_stock=0');
        $response->assertStatus(200);
        $response->assertSee('MacBook Pro');
        $response->assertSee('Design Patterns');
        $response->assertDontSee('iPhone 15');
    }

    public function test_filter_by_rating_from(): void
    {
        $this->createProducts();
        $response = $this->get('/?rating_from=4.8');
        $response->assertStatus(200);
        $response->assertSee('MacBook Pro');
        $response->assertSee('iPhone 15');
        $response->assertDontSee('Clean Code');
    }

    public function test_sort_by_price_asc(): void
    {
        $this->createProducts();
        $response = $this->get('/?sort=price_asc');
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $names = $products->pluck('name')->toArray();
        $this->assertEquals('Clean Code', $names[0]);
        $this->assertEquals('Design Patterns', $names[1]);
        $this->assertEquals('Samsung Galaxy', $names[2]);
        $this->assertEquals('iPhone 15', $names[3]);
        $this->assertEquals('MacBook Pro', $names[4]);
    }

    public function test_sort_by_price_desc(): void
    {
        $this->createProducts();
        $response = $this->get('/?sort=price_desc');
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $names = $products->pluck('name')->toArray();
        $this->assertEquals('MacBook Pro', $names[0]);
        $this->assertEquals('iPhone 15', $names[1]);
        $this->assertEquals('Samsung Galaxy', $names[2]);
        $this->assertEquals('Design Patterns', $names[3]);
        $this->assertEquals('Clean Code', $names[4]);
    }

    public function test_sort_by_rating_desc(): void
    {
        $this->createProducts();
        $response = $this->get('/?sort=rating_desc');
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $names = $products->pluck('name')->toArray();
        $this->assertEquals('MacBook Pro', $names[0]);
        $this->assertEquals('iPhone 15', $names[1]);
    }

    public function test_sort_by_newest(): void
    {
        $this->createProducts();
        $response = $this->get('/?sort=newest');
        $response->assertStatus(200);
    }

    public function test_pagination(): void
    {
        $this->createProducts(20);
        $response = $this->get('/');
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertEquals(5, $products->total());
        $this->assertEquals(5, $products->count());
    }

    public function test_combined_filters(): void
    {
        $this->createProducts();
        $response = $this->get('/?q=MacBook&in_stock=0');
        $response->assertStatus(200);
        $response->assertSee('MacBook Pro');
        $response->assertDontSee('iPhone 15');
    }

    public function test_combined_sort_and_filter(): void
    {
        $this->createProducts();
        $response = $this->get('/?in_stock=1&sort=price_asc');
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $names = $products->pluck('name')->toArray();
        $this->assertEquals('Clean Code', $names[0]);
        $this->assertEquals('Samsung Galaxy', $names[1]);
        $this->assertEquals('iPhone 15', $names[2]);
    }
}
