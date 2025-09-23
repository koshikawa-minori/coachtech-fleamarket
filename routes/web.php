<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;


// 商品一覧画面(ログイン前トップページ)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// ログイン必須ページ
Route::middleware('auth')->group(function () {
    // プロフィール画面(mypage.blade.php)
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');

    // プロフィール編集画面(mypage/profile.blade.php)
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    //プロフィール更新
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');


    // 出品画面(sell.blade.php)
    Route::view('/sell', 'sell')->name('sell');
});
