<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Database\QueryException;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        $validated = $request->validated();
        $item = Item::with('seller:id,name')->findOrFail($itemId);

        if ($item->is_sold) {
            return back();
        }

        try {

        $purchased = DB::transaction(function () use ($validated, $item) {

            $affectedRows = Item::where('id', $item->id)
                ->where('is_sold', false)
                ->update(['is_sold' => true]);

            if ($affectedRows === 0) {
                return false;
            }

            Order::create([
                'buyer_user_id' => Auth::id(),
                'item_id' => $item->id,
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building' => $validated['building'] ?? null,
                'payment_method' => (int)$validated['payment_method'],
            ]);

            return true;

            });

            if ($purchased === false) {
                return back();
            }

        } catch (QueryException $exception) {
            return back();
        } catch (\Throwable $exception) {
            return back();
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

