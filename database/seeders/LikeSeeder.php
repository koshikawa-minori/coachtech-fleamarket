<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $items = Item::all();

        foreach($users as $user) {
            $numberOfLikes = rand(0,5);

            //自分以外の出品から候補を作る
            $candidateItems = $items->where('seller_user_id', '!=', $user->id);

            if($numberOfLikes === 0 || $candidateItems->isEmpty()){
                continue;
            }

            //エラー避けるため安全策
            $likedItems = $candidateItems
                ->shuffle()
                ->take(min($numberOfLikes, $candidateItems->count()));

            $likedItemIds = $likedItems->pluck('id');

            // 既存のいいねをそのままに追加
            $user->likes()->syncWithoutDetaching($likedItemIds);
        }
    }
}
