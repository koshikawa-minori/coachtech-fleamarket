<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    use HasFactory;

    public const PAYMENT_CONVENIENCE_STORE_PAYMENT = 1;
    public const PAYMENT_CREDIT_CARD = 2;

    protected $fillable = [
        'buyer_user_id',
        'item_id',
        'postal_code',
        'address',
        'building',
        'payment_method',
    ];

    protected $casts = [
        'payment_method' => 'integer',
    ];

    public const PAYMENT_LABELS = [
        self::PAYMENT_CONVENIENCE_STORE_PAYMENT => 'コンビニ支払い',
        self::PAYMENT_CREDIT_CARD => 'カード支払い',
    ];

    public static function paymentLabel(?string $value, string $default = '選択してください'): string
    {
        if ($value === null || $value === '') return $default;

        return self::PAYMENT_LABELS[(int)$value] ?? $default;
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
