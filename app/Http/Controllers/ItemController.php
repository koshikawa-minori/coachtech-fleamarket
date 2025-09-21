<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = (string)$request->query('keyword', '');

        $itemsQuery = Item::query();

        //検索
        if($keyword !== ''){
            $itemsQuery->where('name', 'like', "%{$keyword}%");
        }

        //自分の出品商品は非表示
        if (Auth::check()){
            $itemsQuery->where('seller_user_id', '!=', Auth::id());
        }

        //検索結果取得
        $items = $itemsQuery->orderByDesc('id')->get();

        return view('items.index',[
            'items' => $items,
            'keyword' => $keyword,
        ]);

    }
}