<?php

use Illuminate\Support\Facades\Route;


// トップページ（商品一覧になる予定）
Route::view('/', 'top')->name('home');

// Fortify用のリダイレクト先（暫定）
Route::view('/home', 'top')->name('home');

Route::middleware('auth')->group(function () {
    // resources/views/mypage.blade.php
    Route::view('/mypage', 'mypage')->name('mypage');
    // resources/views/sell.blade.php
    Route::view('/sell', 'sell')->name('sell');
    // resources/views/mypage/profile.blade.php
    Route::view('/mypage/profile', 'mypage.profile')->name('mypage.profile');
});
