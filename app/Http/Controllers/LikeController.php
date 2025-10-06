<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class LikeController extends Controller
{
    public function store($itemId)
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        $user->likes()->syncWithoutDetaching([$item->id]);

        return back();

    }

    public function destroy($itemId)
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        $user->likes()->detach($item->id);

        return back();

    }

}
