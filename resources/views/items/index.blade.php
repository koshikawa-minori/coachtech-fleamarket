@extends('layouts.app')

@section('title', '商品一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layouts/item-list.css') }}">
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endpush

@section('content')
<main class="items">
    <div class="items__tabs">
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'recommend'])}}" class="items__tab {{ $currentTab === 'recommend' ? 'items__tab--active' : ''}}">おすすめ
        </a>
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'mylist'])}}" class="items__tab {{ $currentTab === 'mylist' ? 'items__tab--active' : ''}}">マイリスト</a>
    </div>

    <ul class="items__list">
        @foreach ($items as $item)
        <li class="item-card">
            @if($item->is_sold)
                <span class="item-card__sold">Sold</span>
            @endif
            <!-- あとで詳細へリンク-->
            <a class="item-card__link" href="#">
                @if (filled($item->image_path))
                    <img class="item-card__image" src="{{ $item->image_path }}" alt="{{ $item->name }}">
                @else
                    <div class="item-card__image item-card__image--placeholder">商品画像</div>
                @endif
            </a>

            <p class="item-card__name">{{ $item->name }}</p>
        </li>
        @endforeach
    </ul>
</main>
@endsection




