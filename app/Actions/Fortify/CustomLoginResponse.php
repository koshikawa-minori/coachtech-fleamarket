<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        $user = Auth::user();

        //初回判定
        $isFirst = !$user->profile()->exists();

        //初回はプロフィール編集
        if($isFirst) {
            return redirect()->route('profile.edit');
        }
        //２回目以降マイページへ
        return redirect()->intended(route('mypage'));

    }
}