<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'image_path',
        'postal_code',
        'address',
        'building',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

