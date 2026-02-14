@extends('layouts.app')

@section('title', 'マイページ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layouts/item-list.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<main class="mypage">
    <div class="mypage__header">
        <div class="mypage__profile">
            <div class="mypage__image-wrapper">
                @if($profile?->image_url)
                    <img class="mypage__image" src="{{ $profile->image_url }}" alt="プロフィール画像">
                @else
                    <div class="mypage__image--default"></div>
                @endif
            </div>
            <div class="mypage__user-text">
                <p class="mypage__username">{{ $user->name }}</p>
                <p>名前下に☆☆</p>
            </div>
        </div>
        <a class="mypage__edit-button" href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>

    <div class="tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab {{ request('page', 'sell') === 'sell' ? 'tab--active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab {{ request('page') === 'buy' ? 'tab--active' : '' }}">購入した商品</a>
        <a href="{{ route('mypage', ['page' => 'transaction']) }}" class="tab {{ request('page') === 'transaction' ? 'tab--active' : '' }}">取引中の商品
        @if ( $totalUnreadCount >= 1 )
            <span class="transaction-unread__total">{{ $totalUnreadCount }}</span>
        @endif
        </a>
    </div>

    @if ($page === 'transaction')
        <ul class="mypage__items items__list">
            @foreach ($transactions as $transaction)
                <li class="mypage__item item-card">
                    <a class="item-card__link" href="{{ route('transaction.show', ['transactionId' => $transaction->id]) }}">
                        <div class="item-card__thumb">
                            @if ($transaction->item->image_url)
                                <img class="item-card__image" src="{{ $transaction->item->image_url }}" alt="{{ $transaction->item->name }}">
                            @else
                                <div class="item-card__image--placeholder">
                                    商品画像
                                </div>
                            @endif
                            @if ( $transaction->unread_count >= 1 )
                                <span class="transaction-unread__badge">{{ $transaction->unread_count }}</span>
                            @endif
                        </div>
                        <p class="item-card__name">{{ $transaction->item->name }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <ul class="mypage__items items__list">
            @foreach ($items as $item)
                <li class="mypage__item item-card">
                    <div class="item-card__thumb">
                        @if($item->is_sold)
                            <span class="item-card__sold">Sold</span>
                        @endif
                        @if ($item->image_url)
                            <img class="item-card__image" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                        @else
                            <div class="item-card__image--placeholder">
                                商品画像
                            </div>
                        @endif
                    </div>
                    <p class="item-card__name">{{ $item->name }}</p>
                </li>
            @endforeach
        </ul>
    @endif
</main>
@endsection
