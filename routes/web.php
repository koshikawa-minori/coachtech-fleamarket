<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;


// 商品一覧画面(トップページ)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

//商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// 認証必須ページ
Route::middleware('auth')->group(function () {
    // プロフィール画面(mypage.blade.php)
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');

    // プロフィール編集画面(mypage/profile.blade.php)
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    //プロフィール更新
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    //コメント投稿
    Route::post('/item/{item_id}/comments', [CommentController::class, 'store'])->name('comments.store');

    // 出品画面(sell.blade.php)
    Route::view('/sell', 'sell')->name('sell');
});
