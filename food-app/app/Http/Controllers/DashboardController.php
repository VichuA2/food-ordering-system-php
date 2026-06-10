<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Order;
use App\Models\Query;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a separate dashboard for each role .
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            $totalUsers = User::count();
            $totalHotels = Hotel::count();
            $totalOrders = Order::count();
            $totalHotelOwners = User::role('Hotel Owner')->count();

            return view('dashboard.admin', compact('totalUsers', 'totalHotels', 'totalOrders', 'totalHotelOwners'));
        } elseif ($user->hasRole('Hotel Owner')) {
            $hotelIds = Hotel::where('user_id', $user->id)->pluck('id');

            $analytics = [
                'total_orders'   => Order::whereIn('hotel_id', $hotelIds)->count(),
                'picked_orders'  => Order::whereIn('hotel_id', $hotelIds)
                    ->whereIn('status', ['Picked', 'Delivered'])
                    ->count(),
                'total_revenue'  => Order::whereIn('hotel_id', $hotelIds)
                    ->whereIn('status', ['Picked', 'Delivered'])
                    ->sum('total_amount')
            ];

            return view('dashboard.hotel_owner', compact('analytics', 'hotelIds'));
        } elseif ($user->hasRole('Employee')) {
            $totalUsers = User::count();
            $totalHotels = Hotel::count();
            $totalQueries = Query::count();
            return view('dashboard.employee', compact('totalUsers', 'totalHotels', 'totalQueries'));
        } elseif ($user->hasRole('Delivery Person')) {
            $assignedOrders = Order::where('delivery_person_id', $user->id)->count();
            $deliveredOrders = Order::where('delivery_person_id', $user->id)->where('status', 'Delivered')->count();
            return view('dashboard.delivery_person', compact('assignedOrders', 'deliveredOrders'));
        } else {
            $orders = Order::where('user_id', $user->id)->latest()->get();
            return view('dashboard.customer', compact('orders'));
        }
    }
}
