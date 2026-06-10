<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueryController extends Controller
{
    /**
     * Display a listing of the queries.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole(['Admin','Employee'])) {
            $queries = Query::with(['user','order'])->latest()->get();
        } else {
            $queries = Query::where('user_id', $user->id)->with('order')->latest()->get();
        }
        return view('queries.index', compact('queries'));
    }

    /**
     * Show the form for creating a new query.
     */
    public function create()
    {
        $orders = Order::where('user_id', Auth::id())->get();
        return view('queries.create', compact('orders'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'order_id' => 'nullable|exists:orders,id'
        ]);

        Query::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'Pending'
        ]);

        return redirect()->back()->with('success', 'Your query has been submitted! Our team will resolve it soon.');
    }

    /**
     * Update the specified query in storage.
     */
    public function update(Request $request, Query $query)
    {
        $query->update([
            'status' => 'Resolved',
            'resolved_by' => Auth::id(),
        ]);
        return redirect()->back()->with('success', 'Query marked as resolved!');
    }
}
