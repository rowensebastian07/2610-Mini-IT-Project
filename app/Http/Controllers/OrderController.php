<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Notifications\ClubNotification;

class OrderController extends Controller
{
    // 🟠 Show user orders
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
                       ->with('items.product')
                       ->get();

        return view('orders.index', compact('orders'));
    }

    // 🟢 Checkout (store order)
    public function store(Request $request)
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // ✅ Ensure user_id is saved so notifications can link correctly
        $order = Order::create([
            'user_id' => auth()->id(),
            'club_id' => $request->club_id,
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        session()->forget('cart');

        // 🟢 Notify buyer of purchase
        $club = $order->club;
        $purchaseMessage = "Your purchase is successful. Please wait for us to review your payment and we will get back to you in 2 days time.";
        $order->user->notify(new ClubNotification($club, $purchaseMessage, 'purchase'));

        return redirect()->route('orders.index')->with('success', 'Order placed!');
    }

    // 🔵 Show single order
    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    // 🟣 Verify payment and notify buyer
    public function verify(Request $request, Order $order)
{
    // Update order with verification status + message
    $order->update([
        'verification_status' => 'verified',
        'message' => $request->message,
    ]);

    $club = $order->club;
    $message = "✅ Verified: {$request->message}";

    // Notify buyer if user exists
    if ($order->user) {
        $order->user->notify(new ClubNotification($club, $message, 'verification'));
    }

    return back()->with('success', 'Order verified and buyer notified.');
}


}
