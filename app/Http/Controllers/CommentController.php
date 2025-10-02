<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Item;

class CommentController extends Controller
{
    public function store(CommentRequest $request, string $itemId)
    {
        $item = Item::findOrFail($itemId);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'comment' => $request->input('comment'),
        ]);

        return redirect()
        ->route('items.show', ['item_id' => $item->id]);
    }
}
