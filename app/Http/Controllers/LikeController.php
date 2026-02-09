<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class LikeController extends Controller
{
    public function store($itemId)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        $user->likes()->syncWithoutDetaching([$item->id]);

        return back();

    }

    public function destroy($itemId)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        $user->likes()->detach($item->id);

        return back();

    }

}
