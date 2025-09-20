<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;


// 商品一覧画面(トップページ)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// Fortify用のリダイレクト先（暫定で商品一覧ページ表示）
Route::view('/home', 'top')->name('home');

// ログイン必須ページ
Route::middleware('auth')->group(function () {
    // プロフィール画面(mypage.blade.php)
    Route::view('/mypage', 'mypage')->name('mypage');

    // プロフィール編集画面(mypage/profile.blade.php)
    Route::view('/mypage/profile', 'mypage.profile')->name('mypage.profile');

    // 出品画面(sell.blade.php)
    Route::view('/sell', 'sell')->name('sell');
});
