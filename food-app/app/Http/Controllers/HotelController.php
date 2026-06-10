<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of the Hotels.
     */
    public function index(Request $request)
    {
        $query = Hotel::query()->with('users');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($request->filled('owner_id')) {
            $query->where('user_id', $request->owner_id);
        }

        $hotels = $query->paginate(10)->appends($request->all());
        $owners = User::role('Hotel Owner')->orderBy('name')->get();

        return view('hotels.index', compact('hotels', 'owners'));
    }

    /**
     * Show the form for creating a new Hotel.
     */
    public function create()
    {
        $owners = User::role('Hotel Owner')->get();
        return view('hotels.create', compact('owners'));
    }

    /**
     * Store a newly created hotel in storage.
     */
    public function store(StoreHotelRequest $request)
    {
        Hotel::create($request->validated());
        return redirect()->route('hotels.index')->with('success', 'Hotel created successfully.');
    }

    /**
     * Display the specified hotel.
     */
    public function show(Hotel $hotel)
    {
        $hotel->load(['users', 'products.category']);
        return view('hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified hotel.
     */
    public function edit(Hotel $hotel)
    {
        $owners = User::role('Hotel Owner')->get();
        return view('hotels.edit', compact('hotel', 'owners'));
    }

    /**
     * Update the specified hotel in storage.
     */
    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        $hotel->update($request->validated());
        return redirect()->route('hotels.index')->with('success', 'Hotel updated successfully.');
    }

    /**
     * Remove the specified hotel from storage.
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('hotels.index')->with('success', 'Hotel deleted successfully.');
    }
}
