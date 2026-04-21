<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_products_with_pagination(): void
    {
        $category = Category::factory()->create();
        Product::factory()->for($category)->count(25)->create();

        $response = $this->getJson('/api/products?per_page=10');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'price', 'category_id', 'category' => ['id', 'name']]],
                'links',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ])
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonCount(10, 'data');
    }

    public function test_filters_products_by_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        Product::factory()->for($categoryA)->count(3)->create();
        Product::factory()->for($categoryB)->count(2)->create();

        $response = $this->getJson("/api/products?category_id={$categoryA->id}");

        $response->assertOk()
            ->assertJsonPath('meta.total', 3);
    }

    public function test_searches_products_by_name(): void
    {
        $category = Category::factory()->create();
        Product::factory()->for($category)->create(['name' => 'Unique Apple Item']);
        Product::factory()->for($category)->count(5)->create();

        $response = $this->getJson('/api/products?search=apple');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Unique Apple Item');
    }

    public function test_shows_single_product(): void
    {
        $product = Product::factory()->for(Category::factory())->create();

        $this->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.category.id', $product->category_id);
    }

    public function test_show_returns_404_for_unknown_product(): void
    {
        $this->getJson('/api/products/999999')->assertNotFound();
    }

    public function test_store_requires_authentication(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/products', [
            'name' => 'Something',
            'price' => 10,
            'category_id' => $category->id,
        ])->assertUnauthorized();
    }

    public function test_store_validates_input(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);

        $this->postJson('/api/products', [
            'name' => '',
            'price' => 0,
            'category_id' => 999999,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'category_id']);
    }

    public function test_store_creates_product(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $payload = [
            'name' => 'Test product',
            'description' => 'A thing',
            'price' => 19.99,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Test product')
            ->assertJsonPath('data.category.id', $category->id);

        $this->assertDatabaseHas('products', [
            'name' => 'Test product',
            'category_id' => $category->id,
        ]);
    }

    public function test_update_modifies_product(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);
        $product = Product::factory()->for(Category::factory())->create();

        $this->putJson("/api/products/{$product->id}", [
            'name' => 'Renamed',
            'price' => 42.5,
            'category_id' => $product->category_id,
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Renamed');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Renamed',
        ]);
    }

    public function test_destroy_soft_deletes_product(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);
        $product = Product::factory()->for(Category::factory())->create();

        $this->deleteJson("/api/products/{$product->id}")->assertNoContent();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_update_requires_authentication(): void
    {
        $product = Product::factory()->for(Category::factory())->create();

        $this->putJson("/api/products/{$product->id}", [
            'name' => 'x',
            'price' => 1,
            'category_id' => $product->category_id,
        ])->assertUnauthorized();
    }

    public function test_destroy_requires_authentication(): void
    {
        $product = Product::factory()->for(Category::factory())->create();

        $this->deleteJson("/api/products/{$product->id}")->assertUnauthorized();
    }
}
