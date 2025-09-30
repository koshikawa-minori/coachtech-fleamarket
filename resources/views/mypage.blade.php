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
                @if($hasImage)
                    <img class="mypage__image" src="{{ asset('storage/'.$profile->image_path) }}" alt="プロフィール画像">
                @else
                    <div class="mypage__image--default"></div>
                @endif
            </div>
            <p class="mypage__username">{{ $user->name }}</p>
        </div>
        <a class="mypage__edit-button" href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>

    <div class="mypage__tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="mypage__tab {{ request('page', 'sell') === 'sell' ? 'mypage__tab--active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="mypage__tab {{ request('page') === 'buy' ? 'mypage__tab--active' : '' }}">購入した商品</a>
    </div>

    <ul class="mypage__items items__list">
        @foreach ($items as $item)
            <li class="mypage__item item-card">
                @if (filled($item->image_path))
                    <img class="item-card__image" src="{{ $item->image_path }}" alt="{{ $item->name }}">
                @else
                    <div class="item-card__image item-card__image--placeholder">
                        商品画像
                    </div>
                @endif
                <p class="item-card__name">{{ $item->name }}</p>
            </li>
        @endforeach
    </ul>
</main>
@endsection

