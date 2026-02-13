<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Item;
use App\Models\TransactionMessage;
use App\Models\Evaluation;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_user_id',
        'seller_user_id',
        'buyer_read_at',
        'seller_read_at',
        'situation',
    ];

    protected $casts = [
        'buyer_read_at' => 'datetime',
        'seller_read_at' => 'datetime',
        'situation' => 'integer',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function transactionMessages()
    {
        return $this->hasMany(TransactionMessage::class, 'transaction_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'transaction_id');
    }

}
