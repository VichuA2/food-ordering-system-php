<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Product;

class CustomerController extends Controller
{
    /**
     * Display a listing of the Hotel.
     */
    public function index()
    {
        $hotels = Hotel::with('users')->get();
        return view('customer.hotels', compact('hotels'));
    }

    /**
     * Display the Hotel menu.
     */
    public function menu(Hotel $hotel)
    {
        $products = Product::where('hotel_id', $hotel->id)
                                ->where('is_available', true)
                                ->with('category')->get();
        return view('customer.hotel_menu', compact('hotel', 'products'));
    }
}
