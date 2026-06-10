<?php

use App\Models\User;
use App\Models\Hotel;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(\Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin']);
    Role::firstOrCreate(['name' => 'Hotel Owner']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');

    $this->owner = User::factory()->create();
    $this->owner->assignRole('Hotel Owner');

    $this->category = Category::create(['name' => 'Non-Veg']);
    $this->hotel = Hotel::create([
        'name' => 'Aasife Biriyani',
        'user_id' => $this->owner->id
    ]);
});

it('allows admin to view product index', function () {
    $this->actingAs($this->admin)
        ->get(route('products.index'))
        ->assertStatus(200)
        ->assertViewIs('products.index');
});

it('allows hotel owner to create a product', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('biryani.jpg');

    $productData = [
        'name' => 'Chicken Biryani',
        'price' => 250,
        'hotel_id' => $this->hotel->id,
        'category_id' => $this->category->id,
        'is_available' => true,
        'image' => $file
    ];

    $this->actingAs($this->owner)
        ->post(route('products.store'), $productData)
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('success', 'Food Item created successfully.');

    $this->assertDatabaseHas('products', [
        'name' => 'Chicken Biryani',
        'price' => 250,
        'hotel_id' => $this->hotel->id,
    ]);
});

it('allows hotel owner to toggle product availability', function () {
    $product = Product::create([
        'name' => 'Mutton Biryani',
        'price' => 300,
        'hotel_id' => $this->hotel->id,
        'category_id' => $this->category->id,
        'is_available' => true,
    ]);

    $this->actingAs($this->owner)
        ->patch(route('products.toggle', $product))
        ->assertRedirect()
        ->assertSessionHas('success', 'Product status updated!');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'is_available' => false,
    ]);
});

it('allows hotel owner to delete their product', function () {
    $product = Product::create([
        'name' => 'Grill Chicken',
        'price' => 400,
        'hotel_id' => $this->hotel->id,
        'category_id' => $this->category->id,
        'is_available' => true,
    ]);

    $this->actingAs($this->owner)
        ->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('success', 'Food Item deleted successfully.');

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});

it('allows hotel owner to view edit page for their product', function () {
    $product = Product::create([
        'name' => 'Chicken 65',
        'price' => 200,
        'hotel_id' => $this->hotel->id,
        'category_id' => $this->category->id,
        'is_available' => true,
    ]);

    $this->actingAs($this->owner)
        ->get(route('products.edit', $product))
        ->assertStatus(200)
        ->assertViewIs('products.edit')
        ->assertViewHas('product');
});

it('allows hotel owner to update their product', function () {
    $product = Product::create([
        'name' => 'Chicken 65',
        'price' => 200,
        'hotel_id' => $this->hotel->id,
        'category_id' => $this->category->id,
        'is_available' => true,
    ]);

    $updatedData = [
        'name' => 'Spicy Chicken 65',
        'price' => 220,
        'hotel_id' => $this->hotel->id,
        'category_id' => $this->category->id,
        'is_available' => true,
    ];

    $this->actingAs($this->owner)
        ->put(route('products.update', $product), $updatedData)
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('success', 'Food Item updated successfully.');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Spicy Chicken 65',
        'price' => 220,
    ]);
});
