<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Notifications\ClubNotification;

class OrderController extends Controller
{
    //  Show user orders
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
                       ->with('items.product')
                       ->get();

        return view('marketplace.index', compact('orders'));
    }

    //  Checkout (store order)
   public function store(Request $request)
{
    //  Get cart from session
    $cart = session('cart', []);
    if (empty($cart)) {
        return back()->with('error', 'Your cart is empty.');
    }

    //  Calculate total
    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

    //  Get first product safely
    $firstItem = reset($cart);
    $firstProductId = $firstItem['id'];
    $clubId = Product::find($firstProductId)->club_id;

    //  Create order
    $order = Order::create([
        'user_id'    => auth()->id(),
        'club_id'    => $clubId,
        'product_id' => $firstProductId,
        'total'      => $total,
        'status'     => 'pending',
    ]);

    //  Create order items
    foreach ($cart as $item) {
        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $item['id'],
            'quantity'   => $item['quantity'],
            'price'      => $item['price'],
        ]);
    }

    //  Clear cart after checkout
    session()->forget('cart');

    //  Notify buyer of purchase
    $club = $order->club;
    $purchaseMessage = "Your purchase is successful. Please wait for us to review your payment and we will get back to you in 2 days time.";
    $order->user->notify(new ClubNotification($club, $purchaseMessage, 'purchase'));
    $treasurer = $club->treasurer;

    //  Show payment page directly with order + treasurer
    return view('marketplace.payment', compact('order', 'treasurer'))
           ->with('success', 'Order placed! Proceed to payment.');
}

    //  Show single order
    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    //  Verify payment and notify buyer
    public function verify(Request $request, Order $order)
    {
        $order->update([
            'verification_status' => 'verified',
            'message' => $request->message,
        ]);

        $club = $order->club;
        $message = "✅ Verified: {$request->message}";

        if ($order->user) {
            $order->user->notify(new ClubNotification($club, $message, 'verification'));
        }

        return back()->with('success', 'Order verified and buyer notified.');
    }
}

