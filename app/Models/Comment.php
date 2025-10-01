<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $fillable = [
        'item_id',
        'user_id',
        'comment',
    ];

    // 1つの商品に紐づくコメント
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // コメントを投稿したユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
