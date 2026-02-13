<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Transaction;

class TransactionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'sender_id',
        'message',
        'image_path',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
