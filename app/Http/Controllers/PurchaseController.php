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
    public function create(int $item)
    {

        $item = Item::with('seller:id,name')->findOrFail($item);

        //購入済なら戻す
        if ($item->is_sold) {
            return back();
        }

        //購入者
        $authenticatedUser = Auth::user();
        $profile = $authenticatedUser->profile;

        return view('purchase.purchase', compact('item', 'authenticatedUser', 'profile'));
    }

    public function store(PurchaseRequest $request, int $item)
    {

        $validated = $request->validated();

        $item = Item::with('seller:id,name')->findOrFail($item);

        if ($item->is_sold) {
            return back();
        }

        try {
            DB::transaction(function () use ($validated, $item) {

            Order::create([
                'buyer_user_id' => Auth::id(),
                'item_id' => $item->id,
                'shipping_postal_code' => $validated['postal_code'],
                'shipping_address' => $validated['address'],
                'shipping_building' => $validated['building'] ?? null,
                'payment_method' => $validated['payment_method'],
            ]);

            $affectedRows = Item::where('id', $item->id)
                ->where('is_sold', false)
                ->update(['is_sold' => true]);

                if($affectedRows === 0) {
                    throw new \RuntimeException('Already sold');
                }
            });

        } catch (QueryException $exception) {
            return back();
        } catch (\Throwable $exception) {
            return back();
        }

        return redirect()->route('items.index');
    }

    public function edit(int $item_id)
    {
        $authenticatedUser = Auth::user();
        $profile = $authenticatedUser->profile;
        $item = Item::findOrFail($item_id);

        return view('purchase.address', compact('item', 'profile'));
    }

    public function update(AddressRequest $request, int $item_id)
    {
        $authenticatedUser = Auth::user();
        $profile = $authenticatedUser->profile;

        $validated = $request->validated();

        $profile->fill([
            'postal_code' => $validated['postal_code'] ?? null,
            'address' => $validated['address'] ?? null,
            'building' => $validated['building'] ?? null,
        ])->save();

        return redirect()->route('purchase.create', $item_id);
    }

}

