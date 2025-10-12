<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeCheckout;


class PurchaseController extends Controller
{
    public function create(int $itemId)
    {

        $item = Item::with('seller:id,name')->findOrFail($itemId);

        //購入済なら戻す
        if ($item->is_sold) {
            return back();
        }

        //購入者
        $authenticatedUser = Auth::user();
        $profile = $authenticatedUser->profile;

        return view('purchase.purchase', compact('item', 'authenticatedUser', 'profile'));
    }

    public function store(PurchaseRequest $request, int $itemId)
    {
        return $this->pay($request, $itemId);
    }

    public function pay(PurchaseRequest $request, int $itemId)
    {
        $validated = $request->validated();
        $item = Item::with('seller:id,name')->findOrFail($itemId);

        if ($item->is_sold) {
            return back();
        }

    Stripe::setApiKey(config('services.stripe.secret'));

    $paymentMethodTypes = ((int)$validated['payment_method'] === Order::PAYMENT_CREDIT_CARD)
    ? ['card']
    : ['konbini'];

    $successUrl = route('purchase.paid', ['itemId' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}';
    $cancelUrl  = route('purchase.create', ['itemId' => $item->id]);

    $session = StripeCheckout::create([
        'mode' => 'payment',
        'payment_method_types' => $paymentMethodTypes,
        'line_items' => [[
            'price_data' => [
                'currency' => env('STRIPE_CURRENCY', 'jpy'),
                'unit_amount' => (int)$item->price,
                'product_data' => ['name' => $item->name],
            ],
            'quantity' => 1,
        ]],
        'customer_email' => Auth::user()->email,
        'metadata' => [
            'user_id' => (string)Auth::id(),
            'item_id' => (string)$item->id,
            'payment_method' =>(string)$validated['payment_method'],
            'postal_code' => (string)$validated['postal_code'],
            'address' => (string)$validated['address'],
            'building' => (string)($validated['building'] ?? ''),
        ],
        'success_url' => $successUrl,
        'cancel_url' => $cancelUrl,

        ]);
        return redirect()->away($session->url);
    }

    public function paid(int $itemId)
    {
        $item = Item::with('seller:id,name')->findOrFail($itemId);
        $sessionId = request('session_id');

        if(empty($sessionId)) {
            return redirect()->route('purchase.create', $itemId);
        }

        if ($item->is_sold) {
        return redirect()->route('items.index');
        }

        Stripe::setApiKey(config(('services.stripe.secret')));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        $meta = $session->metadata ?? null;
        if (!$meta || (int)$meta->item_id !== $item->id) {
            return redirect()->route('items.index');
        }

        if ($session->payment_status === 'paid') {
            try {
                DB::transaction(function () use ($item, $meta) {
                    $affectedRows = Item::where('id', $item->id)
                        ->where('is_sold', false)
                        ->update(['is_sold' => true]);
                    if ($affectedRows === 0) {
                        throw new \RuntimeException();
                    }
                    Order::create([
                        'buyer_user_id' => (int)$meta->user_id,
                        'item_id' =>  (int)$meta->item_id,
                        'payment_method' =>  (int)$meta->payment_method,
                        'postal_code' => (string)$meta->postal_code,
                        'address' => (string)$meta->address,
                        'building' => (string)($meta->building ?? ''),
                    ]);
                });
            } catch (\Throwable $exception) {
                return redirect()->route('items.index');
            }
            return redirect()->route('items.index');
        }

        return redirect()->route('items.index');

    }

    public function edit(int $itemId)
    {
        $authenticatedUser = Auth::user();
        $profile = $authenticatedUser->profile;
        $item = Item::findOrFail($itemId);

        return view('purchase.address', compact('item', 'profile'));
    }

    public function update(AddressRequest $request, int $itemId)
    {
        $authenticatedUser = Auth::user();
        $profile = $authenticatedUser->profile;

        $validated = $request->validated();

        $profile->fill([
            'postal_code' => $validated['postal_code'] ?? null,
            'address' => $validated['address'] ?? null,
            'building' => $validated['building'] ?? null,
        ])->save();

        return redirect()->route('purchase.create', $itemId);
    }

}

