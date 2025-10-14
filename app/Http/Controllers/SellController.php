<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function create() {

        $categories = Category::orderBy('id')->get(['id', 'name']);

        $conditions = [
            1=> '良好',
            2=> '目立った傷や汚れなし',
            3=> 'やや傷や汚れあり',
            4=> '状態が悪い',
        ];

        return view('items.sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request) {
        $validated = $request->validated();

        $storedPath = $request->file('image')->store('items', 'public');

        $createdItem = DB::transaction(function () use ($validated,$storedPath) {
            /** @var \App\Models\Item $item */
            $item = Item::create([
                'seller_user_id' => Auth::id(),
                'name' => $validated['name'],
                'brand_name' => $validated['brand'] ?? null,
                'price' => $validated['price'],
                'description' => $validated['description'],
                'condition' => (int)$validated['condition'],
                'image_path' => $storedPath,
                'is_sold' => false,
            ]);

            if (!empty($validated['category_ids'])) {
                $item->categories()->sync($validated['category_ids']);
            }

            return $item;

        });

        return redirect()->route('items.show', $createdItem->id);
    }
}
