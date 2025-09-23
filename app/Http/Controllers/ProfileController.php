<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    //プロフィール画面表示
    public function show(){
        $user = Auth::user();
        $profile = $user->profile;

        $page = request('page', 'sell');
        $items = $page === 'buy'
        ? $user->purchasedItems
        : $user->items;

        return view('mypage', compact('user', 'profile'));
    }

    //プロフィール編集画面表示
    public function edit(){
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile', compact('user', 'profile'));

    }

    //更新処理等
    public function update(ProfileRequest $request)
    {
        //認証ユーザー取得
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();

        $isFirst = !$authenticatedUser->profile()->exists();

        //users.nameを更新
        $authenticatedUser->update([
            'name' => $request->input('name'),
        ]);

        //画像
        $newImagePath = null;
        if ($request->hasFile('image_path')) {
            $newImagePath = $request->file('image_path')->store('profiles', 'public');
        }

        //プロフィール更新と作成
        $authenticatedUser->profile()->updateOrCreate(
            ['user_id' => $authenticatedUser->id],
            [
                'image_path' => $newImagePath ?? optional($authenticatedUser->profile)->image_path,
                'postal_code' => $request->input('postal_code'),
                'address' => $request->input('address'),
                'building' => $request->input('building'),
            ]
        );

        return $isFirst
        ? redirect()->route('items.index')->with('success', '初回設定が完了しました。')
        : redirect()->route('mypage')->with('success', 'プロフィールを更新しました');
    }
}

