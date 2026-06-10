<?php

namespace App\Http\Controllers;

use App\Events\OrderPicked;
use App\Events\OrderPlaced;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the Orders.
     */
    public function index()
    {
        $user = Auth::user();
        $orders = collect();

        if ($user->hasRole('Admin')) {
            $orders = Order::with(['customer','hotel','deliveryPerson'])->latest()->get();
        } elseif ($user->hasRole('Hotel Owner')) {
            $hotelIds = Hotel::where('user_id', $user->id)->pluck('id');
            $orders = Order::whereIn('hotel_id', $hotelIds)->with(['customer','deliveryPerson'])->latest()->get();
        } elseif ($user->hasRole('Delivery Person')) {
            $orders = Order::whereNull('delivery_person_id')
                            ->orWhere('delivery_person_id', $user->id)
                            ->with(['customer','hotel'])
                            ->latest()
                            ->get();
        } else {
            $orders = Order::where('user_id', $user->id)->with(['hotel','deliveryPerson'])->latest()->get();
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified Order.
     */
    public function show(Order $order)
    {
        $order->load(['customer','hotel','deliveryPerson','orderitems.product']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for creating a new Order.
     */
    public function create()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart || $cart->cartItems()->count() == 0) {
            return redirect()->route('customer.hotels')->with('error', 'Your cart is empty');
        }

        $cartItems = CartItem::with('product.hotel')->where('cart_id', $cart->id)->get();
        $grandTotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return view('orders.checkout', compact('cartItems', 'grandTotal'));
    }

    /**
     * Store a newly created Order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_location' => 'required|string|max:255',
            'payment_method' => 'required|string|in:Cash On Delivery,Card,UPI'
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart) {
            return redirect()->back();
        }

        $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();
        $groupedItems = $cartItems->groupBy('product.hotel_id');

        DB::beginTransaction();

        try {
            $createdOrders = [];

            foreach ($groupedItems as $hotelId => $items) {
                $totalAmount = $items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'hotel_id' => $hotelId,
                    'total_amount' => $totalAmount,
                    'delivery_location' => $request->delivery_location,
                    'status' => 'Pending'
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ]);
                }
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $totalAmount,
                    'status' => 'Pending'
                ]);

                $createdOrders[] = $order;
            }
            foreach ($createdOrders as $order) {
                event(new OrderPlaced($order));
            }

            CartItem::where('cart_id', $cart->id)->delete();
            $cart->delete();
            DB::commit();
            return view('orders.success')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    /**
     * Update the specified Order in storage.
     */
    public function pick(Order $order)
    {
        $this->authorize('pick', $order);
        $user = Auth::user();
        if (!$user->hasRole('Delivery Person')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($order->delivery_person_id) {
            return redirect()->back()->with('error', 'Order is already assigned.');
        }

        $order->update([
            'delivery_person_id' => $user->id,
            'status' => 'Picked'
        ]);

        $order->load(['customer', 'hotel', 'deliveryPerson']);

        event(new OrderPicked($order));
        return redirect()->back()->with('success', 'You have picked this order.');
    }

    /**
     * Update the status of the specified Order in storage.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $request->validate(['status' => 'required|string']);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
