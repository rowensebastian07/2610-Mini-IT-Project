<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // 🟢 Show cart (GET /cart)
    public function index()
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('marketplace.cart', compact('cart', 'total'));
    }

    // 🟠 Add product to cart (POST /cart)
   public function store(Request $request)
{
    $product = Product::findOrFail($request->product_id);
    $cart = session()->get('cart', []);

    $cart[$product->id] = [
        'id'       => $product->id,
        'name'     => $product->name,
        'price'    => $product->price,
        'quantity' => ($cart[$product->id]['quantity'] ?? 0) + $request->quantity,
    ];

    session()->put('cart', $cart);

    return redirect()->route('cart.index')->with('success', "{$product->name} added to cart!");
}


    // 🔵 Remove product from cart (DELETE /cart/{id})
   public function destroy($id)
{
    if ($id === 'all') {
        session()->forget('cart');
        return back()->with('success', "Cart cleared!");
    }

    $cart = session()->get('cart', []);
    unset($cart[$id]);
    session()->put('cart', $cart);

    return back()->with('success', "Item removed from cart!");
}


    // 🟣 Update quantity (PUT/PATCH /cart/{id})
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', "Cart updated!");
    }

    // Optional: clear cart (DELETE /cart)
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', "Cart cleared!");
    }
}
