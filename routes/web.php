<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;

// 商品一覧画面(トップページ)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

//商品詳細画面
Route::get('/item/{itemId}', [ItemController::class, 'show'])->name('items.show');

// 認証必須ページ
Route::middleware('auth')->group(function () {
    // プロフィール画面(mypage.blade.php)
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');

    // プロフィール編集画面(mypage/profile.blade.php)
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    //プロフィール更新
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    //コメント投稿
    Route::post('/item/{itemId}/comments', [CommentController::class, 'store'])->name('comments.store');

    //いいね機能
    Route::post('/item/{itemId}/like', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/item/{itemId}/like', [LikeController::class, 'destroy'])->name('likes.destroy');

    //購入画面
    Route::get('/purchase/{itemId}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{itemId}',  [PurchaseController::class, 'store'])->name('purchase.store');

    //Stripe決済処理
    Route::post('/purchase/{itemId}/pay', [PurchaseController::class, 'pay'])->name('purchase.pay');
    Route::get('/purchase/{itemId}/paid', [PurchaseController::class, 'paid'])->name('purchase.paid');

    //住所変更
    Route::get('/purchase/address/{itemId}', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::post('/purchase/address/{itemId}', [PurchaseController::class, 'update'])->name('purchase.update');

    // 出品画面(sell.blade.php)
    Route::view('/sell', 'sell')->name('sell');
});
