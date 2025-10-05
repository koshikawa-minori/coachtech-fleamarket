@extends('layouts.app')

@section('title', '商品詳細')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items/item.css') }}">
@endpush

@section('content')
<main class="item-show">
    <div class="item">
        <a class="item-card" href="#">
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
        </a>

        <div class="item-detail">
            <!-- ブランド名ない時コーチに確認中-->
            <h1 class="item-show__title">{{ $item->name }}</h1>
            @if(filled($item->brand_name))
            <p class="item-show__brand"> {{ ($item->brand_name) }}</p>
            @endif
            <p class="item-show__price">
            <span class="item-show__currency">￥</span> {{ number_format($item->price) }} <span class="item-show__tax">(税込)</span>
            </p>

            <div class="item-show__meta">
                <span class="item-show__likes">
                    @auth
                        @if(!$item->likes->contains(auth()->user()))
                            <form method="POST" action="{{ route('likes.store', $item->id) }}">
                                @csrf
                                <button class="like-button" type="submit">
                                    {{ $item->likes_count }}
                                    <svg class="icon icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="none" stroke="#333" stroke-width="2"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('likes.destroy', $item->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="like-button like-button--active" type="submit">
                                    {{ $item->likes_count }}
                                    <svg class="icon icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="#ff5555" stroke="#ff5555" stroke-width="2"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    @endauth
                </span>

                <span class="item-show__comments">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12c0 5-4 9-9 9a9 9 0 0 1-3-.5L5 21l1-3a9 9 0 1 1 15-6z"/>
                    </svg>
                    {{ $item->comments_count }}
                </span>
            </div>

            @auth
                <form class="purchase-form" method="post" action="{{ route('purchase.store', $item) }}">
                    @csrf
                    <button {{ $item->is_sold ? 'disabled' : '' }}>購入手続きへ</button>
                </form>
            @else
                <a class="button button--purchase" href="{{ route('login') }}">購入手続きへ</a>
            @endauth

            <h2>商品説明</h2>
            <div class="item-show__description">
                {!! nl2br(e($item->description)) !!}
            </div>

            <h2>商品の情報</h2>
            @if ($item->categories->isNotEmpty())
                <div class="item-show__category">
                    <span class="item-show__category-label">カテゴリー</span>
                    <div class="item-show__category-list">
                        @foreach ($item->categories as $category)
                            <span class="item-show__category-badge">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <p class="item-show__condition">
                <span class="item-show__condition-label">商品の状態：</span>
                {{ $item->condition_label }}
            </p>

            <h2 class="comment-title">コメント ({{ $item->comments_count }})</h2>
            @if($comments->isNotEmpty())
                <section class="comments">
                    @foreach ($comments as $comment)
                        @if(filled($comment->comment))
                            <div class="comment">
                                <div class="comment__profile">
                                    <div class="comment__image-wrapper">
                                        @if ($comment->user->profile && filled($comment->user->profile->image_path))
                                            <img class="comment__image"
                                            src="{{ asset('storage/'.$comment->user->profile->image_path) }}"
                                            alt="プロフィール画像">
                                        @else
                                        <div class="comment__image--default"></div>
                                        @endif
                                    </div>
                                    <p class="comment__username">{{ $comment->user->name }}</p>
                                </div>
                                <p class="comment__body">{{ $comment->comment }}</p>
                            </div>
                        @endif

                    @endforeach
                </section>
            @endif

            <h2 class="item-detail__title">商品へのコメント</h2>
            @auth
                <form class="comment-form" method="post" action="{{ route('comments.store', $item) }}">
                    @csrf
                    <textarea class="comment-form__textarea" name="comment" rows="5">{{ old('comment') }}</textarea>
                    @error('comment')
                    <p class="comment--error">{{ $message }}</p>
                    @enderror

                    <button>コメントを送信する</button>
                </form>
            @else
                <form class="comment-form">
                    <textarea class="comment-form__textarea" rows="5" disabled></textarea>
                    <a class="button button--primary" href="{{ route('login') }}">コメントを送信する</a>
                </form>
            @endauth
        </div>
    </div>
</main>
@endsection