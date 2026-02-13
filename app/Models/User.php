<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Models\Evaluation;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'seller_user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_user_id');
    }

    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'orders', 'buyer_user_id', 'item_id')->withTimestamps();
    }

    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')->withTimestamps();
    }

    public function buyTransactions()
    {
        return $this->hasMany(Transaction::class, 'buyer_user_id');

    }

    public function sellTransactions()
    {
        return $this->hasMany(Transaction::class, 'seller_user_id');
    }

    public function transactionMessages()
    {
        return $this->hasMany(TransactionMessage::class, 'sender_id');
    }

    public function givenEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function receivedEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluated_id');
    }

}
