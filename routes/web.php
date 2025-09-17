<?php

use Illuminate\Support\Facades\Route;


// トップページ（商品一覧になる予定）
Route::view('/', 'top')->name('home');

// Fortify用のリダイレクト先（暫定）
Route::view('/home', 'top')->name('home');

Route::middleware('auth')->group(function () {
    Route::view('/mypage', 'mypage')->name('mypage'); // resources/views/mypage.blade.php
    Route::view('/sell', 'sell')->name('sell');       // resources/views/sell.blade.php
});
