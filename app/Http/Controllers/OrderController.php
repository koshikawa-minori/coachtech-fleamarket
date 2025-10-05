<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create(int $item_id)
    {

        $item = Item::with('seller:id,name')->findOrFail($item_id);

        //購入済
        if ($item->is_sold) {
            return back();
        }

        //自分が出品
        if ($item->user_id === Auth::id()) {
            return back();
        }

        //購入者
        $authenticatedUser = Auth::user();
        $profile = $authenticatedUser->profile;

        return view('purchase.purchase', compact('item', 'authenticatedUser', 'profile'));
    }

    //購入処理ダミー【あとで実装】
    public function store(Request $request, int $item_id)
    {
        return redirect()
            ->route('purchase.create', ['item_id' => $item_id])
            ->with('info',  '購入処理は後で実装予定です。現在は確認画面のみ表示します');
    }

}
