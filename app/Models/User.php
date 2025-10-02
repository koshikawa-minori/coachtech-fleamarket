<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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

    public function purchasedItems()
    {
        // orders テーブルを使って購入履歴を管理する想定
        // 中間テーブル名・カラム名に合わせて調整
        return $this->belongsToMany(Item::class, 'orders');
    }

    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')
        ->withTimestamps();
    }

}
