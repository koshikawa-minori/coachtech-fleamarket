<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // プロフィール画面表示
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $profile = $user->profile;

        $page = request('page', 'sell');

        if ($page === 'buy') {
            $items = $user->purchasedItems()->get();
        } else {
            $items = $user->items()->get();
        }

        return view('mypage', compact('user', 'profile', 'items'));

    }

    // プロフィール編集画面表示
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile', compact('user', 'profile'));

    }

    // 更新処理等
    public function update(ProfileRequest $request)
    {
        // 認証ユーザー取得
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();

        $isFirst = !$authenticatedUser->profile()->exists();

        // users.nameを更新
        $authenticatedUser->update([
            'name' => $request->input('name'),
        ]);

        // 画像アップロード
        $newImagePath = null;
        if ($request->hasFile('image_path')) {
            $oldPath = optional($authenticatedUser->profile)->image_path;
            if (filled($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $newImagePath = $request->file('image_path')->store('profile_images', 'public');
        }

        // プロフィール更新と作成
        $currentProfile = $authenticatedUser->profile;
        $imagePathToSave = null;

        if (!empty($newImagePath)) {
            $imagePathToSave = $newImagePath;
        } elseif (!empty($currentProfile) && !empty($currentProfile->image_path)) {
            $imagePathToSave = $currentProfile->image_path;
        } else {
            $imagePathToSave = null;
        }

        $profileData = [
            'user_id' => $authenticatedUser->id,
            'image_path'  => $imagePathToSave,
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ];

        $authenticatedUser->profile()->updateOrCreate(
            ['user_id' => $authenticatedUser->id],
            $profileData
        );

        if ($isFirst) {
            return redirect()->route('items.index');
        } else {
            return redirect()->route('mypage');
        }
    }
}
