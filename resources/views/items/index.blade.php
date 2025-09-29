@extends('layouts.app')

@section('title', '商品一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items.css')}}">
@endpush

@section('content')
<main>
    <nav class="items__tabs">
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'recommend'])}}" class="items-tabs__link {{ $currentTab === 'recommend' ? 'items__tabs--active' : ''}}">おすすめ
        </a>
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'mylist'])}}" class="items-tabs__link {{ $currentTab === 'mylist' ? 'items__tabs--active' : ''}}">マイリスト</a>
    </nav>

    <ul class="item-list">
        @foreach ($items as $item)
        <li class="item-card">
            @if(!empty($item->is_sold) && $item->is_sold)
                <span class="item-card__badge">Sold</span>
            @endif
            <!-- あとで詳細へリンク-->
            <a class="item-card__thumb">
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




