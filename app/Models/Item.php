<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_user_id',
        'name',
        'brand_name',
        'price',
        'description',
        'condition',
        'image_path',
        'is_sold',
    ];

    protected $casts = [
        'is_sold' => 'boolean',
        'price' => 'integer',
        'condition' => 'integer',
    ];

    //画像はstorageへ
    public function getImageUrlAttribute(): ?string{
        if (!filled($this->image_path)) {
            return null;
        }

        if(Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return asset('storage/'.$this->image_path);
    }

    // condition数値 → 文字列
    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            1=> '良好',
            2=> '目立った傷や汚れなし',
            3=> 'やや傷や汚れあり',
            4=> '状態が悪い',
        };
    }

    //出品者
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    //いいね機能
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    //コメント
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    //カテゴリ
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items')->withTimestamps();
    }

}
