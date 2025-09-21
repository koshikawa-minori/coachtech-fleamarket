<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
