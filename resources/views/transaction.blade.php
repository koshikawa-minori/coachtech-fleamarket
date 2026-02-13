@extends('layouts.app')

@section('title', '取引チャット')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
@endpush

@section('content')
<main class="transaction">
    <header class="transaction__header">
        <div class="transaction__profile">
            <div class="transaction__image-wrapper">
                プロフィール画像入る
            </div>
            <p class="transaction__username">「ユーザー」さんとの取引画面</p>
            <button>取引を完了するボタン</button>
        </div>
        <div class="transaction__item">
            商品画像
            商品名
            商品価格
        </div>
    </header>
    <section class="transaction__chat">
            <div>左相手</div>
            <div>右自分</div>
            <div class="transaction__composer">
                <div>messeage入力欄「取引メッセージを記入してください」</div>
                <button>画像を追加ボタン</button>
                <button>紙飛行機ボタン</button>
            </div>
    </section>
</main>
@endsection
