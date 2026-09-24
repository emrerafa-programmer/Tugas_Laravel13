<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Laravel 13 only bypasses CSRF for console unit tests, so disable it
        // for feature tests that perform POST/PUT/DELETE requests.
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_products_page_returns_a_successful_response()
    {
        $response = $this->get('/products');

        $response->assertOk();
        $response->assertSee('Data Products belum ada.');
    }

    public function test_products_page_displays_products_from_the_database()
    {
        $product = Product::create([
            'image' => 'product-1.png',
            'title' => 'Produk Test',
            'description' => 'Deskripsi produk test',
            'price' => 100000,
            'stock' => 10,
        ]);

        $response = $this->get('/products');

        $response->assertOk();
        $response->assertSee('Produk Test');
        $response->assertSee('Rp 100.000,00');
        $response->assertSee('10');
    }

    public function test_create_page_returns_a_successful_response()
    {
        $response = $this->get('/products/create');

        $response->assertOk();
        $response->assertSee('Tambah Product');
    }

    public function test_store_creates_a_product_and_uploads_the_image()
    {
        Storage::fake('public');

        $response = $this->post('/products', [
            'image' => UploadedFile::fake()->image('product.png'),
            'title' => 'Produk Baru',
            'description' => 'Deskripsi produk baru yang panjang',
            'price' => 200000,
            'stock' => 8,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['title' => 'Produk Baru']);
        Storage::disk('public')->assertExists('products/' . Product::first()->image);
    }

    public function test_store_validates_the_form()
    {
        $response = $this->post('/products', [
            'title' => 'abc',
        ]);

        $response->assertSessionHasErrors(['image', 'title', 'description', 'price', 'stock']);
    }

    public function test_show_page_displays_product_detail()
    {
        $product = Product::create([
            'image' => 'product-1.png',
            'title' => 'Produk Detail',
            'description' => 'Deskripsi produk detail',
            'price' => 100000,
            'stock' => 10,
        ]);

        $response = $this->get('/products/' . $product->id);

        $response->assertOk();
        $response->assertSee('Produk Detail');
        $response->assertSee('Rp 100.000,00');
    }

    public function test_edit_page_displays_product_data()
    {
        $product = Product::create([
            'image' => 'product-1.png',
            'title' => 'Produk Edit',
            'description' => 'Deskripsi produk edit',
            'price' => 100000,
            'stock' => 10,
        ]);

        $response = $this->get('/products/' . $product->id . '/edit');

        $response->assertOk();
        $response->assertSee('Produk Edit');
        $response->assertSee('Edit Product');
    }

    public function test_update_changes_product_data()
    {
        $product = Product::create([
            'image' => 'product-1.png',
            'title' => 'Produk Lama',
            'description' => 'Deskripsi produk lama',
            'price' => 100000,
            'stock' => 10,
        ]);

        $response = $this->put('/products/' . $product->id, [
            'title' => 'Produk Diubah',
            'description' => 'Deskripsi produk yang sudah diubah',
            'price' => 99999,
            'stock' => 3,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['title' => 'Produk Diubah']);
    }

    public function test_destroy_deletes_product_and_its_image()
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/product-1.png', 'fake image content');

        $product = Product::create([
            'image' => 'product-1.png',
            'title' => 'Produk Hapus',
            'description' => 'Deskripsi produk hapus',
            'price' => 100000,
            'stock' => 10,
        ]);

        $response = $this->delete('/products/' . $product->id);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/product-1.png');
    }
}