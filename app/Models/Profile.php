<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'image_path',
        'postal_code',
        'address',
        'building',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

