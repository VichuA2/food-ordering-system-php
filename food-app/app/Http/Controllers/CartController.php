<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();
        return view('cart.index', compact('cartItems'));
    }


    /**
     * Add a product to the cart
     */
    public function add(AddToCartRequest $request)
    {
        $validated = $request->validated();

        $product = Product::findOrFail($validated['product_id']);
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Food added to your cart successfully!'
            ]);
        }
        return redirect()->back()->with('success', 'Food added to your cart successfully.');
    }

    /**
     * Remove an item from the cart
    */
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);

        if ($cartItem->cart->user_id == Auth::id()) {
            $cartItem->delete();
            return redirect()->back()->with('success', 'Item removed from cart.');
        }

        return redirect()->back()->with('error', 'Unauthorized action.');
    }
}
