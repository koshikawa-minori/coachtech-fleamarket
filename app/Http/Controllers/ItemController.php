<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {

        $isAuthenticated = Auth::check();
        $authenticatedUserId = $isAuthenticated ? Auth::id() : null;

        $keyword = (string)$request->query('keyword', '');
        $tab = $request->query('tab', $isAuthenticated ? 'mylist' : 'recommend');

        //タブ切り替え
        if ($tab === 'mylist') {
            if (!$isAuthenticated) {
                $items = collect();
            }else{
                 /** @var \App\Models\User $authenticatedUser */
                $authenticatedUser = Auth::user();

                $items = $authenticatedUser->likes()
                ->when($keyword !== '', function ($query) use ($keyword) {
                    $query->where('items.name', 'like', "%{$keyword}%");
                })
                ->where('items.seller_user_id', '!=', $authenticatedUserId)
                ->latest('items.id')
                ->get();
            }
        }else{
            $items = Item::query()
            ->when($isAuthenticated, function ($query) use ($authenticatedUserId) {
                $query->where('seller_user_id', '!=', $authenticatedUserId);
            })
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->latest('id')
            ->get();
        }

        return view('items.index', [
            'items' => $items,
            'tab' => $tab,
            'currentTab' => $tab,
            'keyword' => $keyword
        ]);
    }

    public function show(string $itemId)
    {
        $item = Item::with(['seller:id,name'])
            ->withCount(['likes','comments'])
            ->findOrFail($itemId);

        $comments = $item->comments()->with('user:id,name')->latest('id')->get();

        return view('items.item', [
            'item' => $item,
            'comments' => $comments,
        ]);

    }




}