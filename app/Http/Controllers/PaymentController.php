<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Club;
use App\Models\Treasurer;
use Illuminate\Http\Request;
use App\Notifications\ClubNotification;


class PaymentController extends Controller
{
    /**
     * Show the payment form for a product.
     */
    public function create(Product $product)
    {
        // Get the club that owns this product
        $club = $product->club;

        // Ensure treasurer exists (auto-create if missing)
        if (!$club->treasurer) {
            Treasurer::create([
                'club_id' => $club->id,
                'name' => 'Default Treasurer',
                'bank_name' => 'Not set',
                'account_number' => 'Not set',
                'qr_payment' => null,
            ]);
        }

        $treasurer = $club->treasurer;

        return view('marketplace.payment', compact('product', 'club', 'treasurer'));
    }

  public function store(Request $request, Product $product)
{
    $request->validate([
        'payer_name'   => 'required|string|max:255',
        'amount'       => 'required|numeric',
        'payment_date' => 'required|date',
        'proof_image'  => 'required|image|max:2048',
    ]);

    $path = $request->file('proof_image')->store('payments', 'public');

    $order = \App\Models\Order::create([
         'user_id' => auth()->id(),
        'club_id'             => $product->club_id,
        'product_id'          => $product->id,
        'payer_name'          => $request->payer_name,
        'amount'              => $request->amount,
        'total'               => $request->amount,
        'payment_date'        => $request->payment_date,
        'proof_image'         => $path,
        'verification_status' => 'pending',
    ]);

    // Notify buyer
    $club = $product->club;
    $message = 'YOUR PURCHASE IS SUCCESSFUL. PLEASE WAIT FOR US TO REVIEW YOUR PAYMENT AND WE WILL GET BACK TO YOU IN 2 DAYS TIME.';
    auth()->user()->notify(new ClubNotification($club, $message, 'purchase'));

    return back()->with('success', 'Payment submitted, pending verification. You may leave this page');
}

}
