@extends('layouts.app')

@section('title', '商品一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layouts/item-list.css') }}">
@endpush

@section('content')
<main class="items">
    <div class="tabs">
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'recommend'])}}" class="tab {{ $currentTab === 'recommend' ? 'tab--active' : ''}}">おすすめ
        </a>
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'mylist'])}}" class="tab {{ $currentTab === 'mylist' ? 'tab--active' : ''}}">マイリスト</a>
    </div>

    <ul class="items__list">
        @foreach ($items as $item)
        <li class="item-card">
            <a class="item-card__link" href="{{ route('items.show', ['item_id' => $item->id]) }}">
                <div class="item-card__thumb">
                    @if (filled($item->image_path))
                        <img class="item-card__image" src="{{ $item->image_path }}" alt="{{ $item->name }}">
                    @else
                        <div class="item-card__image item-card__image--placeholder">商品画像</div>
                    @endif

                    @if($item->is_sold)
                        <span class="item-card__sold">Sold</span>
                    @endif
                </div>
                <p class="item-card__name">{{ $item->name }}</p>
            </a>
        </li>
        @endforeach
    </ul>
</main>
@endsection




