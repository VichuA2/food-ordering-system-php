<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Hotel;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the Products.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            $products = Product::with(['hotel','category'])->where('is_available', true)->latest()->get();
        } else {
            $hotelIds = Hotel::where('user_id', $user->id)->pluck('id');
            $products = Product::whereIn('hotel_id', $hotelIds)->with(['hotel','category'])->get();
        }

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new Product.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Category::all();
        if ($user->hasRole('Admin')) {
            $hotels = Hotel::all();
        } else {
            $hotels = Hotel::where('user_id', Auth::id())->get();
        }

        return view('products.create', compact('categories', 'hotels'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $data['image'] = $imageName;
        }

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Food Item created successfully.');
    }

    /**
     * Display the specified Product.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified Product.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $user = Auth::user();
        $categories = Category::all();
        if ($user->hasRole('Admin')) {
            $hotels = Hotel::all();
        } else {
            $hotels = Hotel::where('user_id', $user->id)->get();
        }

        return view('products.edit', compact('product', 'categories', 'hotels'));
    }

    /**
     * Update the specified Product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $data['image'] = $imageName;
        }
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Food Item updated successfully.');
    }

    /**
     * Remove the specified Product from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Food Item deleted successfully.');
    }

    /**
     * Toggle the availability of a Product.
     */
    public function toggleAvailability(Product $product)
    {
        $product->is_available = !$product->is_available;
        $product->save();

        return redirect()->back()->with('success', 'Product status updated!');
    }
}
