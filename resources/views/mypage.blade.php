@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<main class="mypage">
    <div class="mypage__header">
        <div class="mypage__image-wrapper">
            @if($hasImage)
                <img class="mypage__image" src="{{ asset('storage/'.$profile->image_path) }}" alt="プロフィール画像">
            @else
                <div class="mypage__image--default"></div>
            @endif
        </div>
        <p class="mypage__username">{{ $user->name }}</p>
        <a class="mypage__edit-button" href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>

    <div class="mypage__tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="mypage__tab {{ request('page', 'sell') === 'sell' ? 'mypage__tab--active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="mypage__tab {{ request('page') === 'buy' ? 'mypage__tab--active' : '' }}">購入した商品</a>
    </div>

    <div class="mypage__items">
        <!-- コーチに確認中 profile controllerもいじってる-->
        @if($items->isEmpty())
            <p class="mypage__empty">商品がありません</p>
        @else
            @foreach ($items as $item)
            <div class="mypage__item">
                <img class="mypage__item-image" src="{{ $item->image_path }}" alt="商品画像">
                <p class="mypage__item-name">{{ $item->name }}</p>
            </div>
            @endforeach
        @endif
    </div>
</main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mypage.css')}}">
@endpush
