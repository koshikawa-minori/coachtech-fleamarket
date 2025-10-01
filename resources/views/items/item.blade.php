@extends('layouts.app')

@section('title', '商品詳細')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layouts/item-list.css') }}">
<link rel="stylesheet" href="{{ asset('css/layouts/items/item.css') }}">
@endpush

@section('content')
<main class="item-show">
    <div class="item">
        <a class="item-card" href="#">
            @if (filled($item->image_path))
                <img class="item-card__image" src="{{ $item->image_path }}" alt="{{ $item->name }}">
            @else
                <div class="item-card__image item-card__image--placeholder">商品画像</div>
            @endif
        </a>

        <div class="item-detail">
            <h1 class="item-show__title">{{ $item->name }}</h1>
            <h3>ブランド名</h3>
            <!-- 金額 -->

            <!-- いいね -->
            <!-- コメント -->
            <button>購入手続きへ</button>

            <h2>商品説明</h2>
            <!-- カラー  状態  コメント -->

            <h2>商品の情報</h2>
            <!-- カテゴリ -->
            <!-- 商品の状態 -->

            <h2>コメント</h2>
            <!-- 画像　＋ ユーザー名　-->
            <!-- コメント -->

            <h2>商品へのコメント</h2>
            <!-- 入力欄 -->

            <button>コメントを送信する</button>

        </div>
    </div>
</main>
@endsection