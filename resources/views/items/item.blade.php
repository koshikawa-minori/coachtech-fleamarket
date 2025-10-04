@extends('layouts.app')

@section('title', '商品詳細')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items/item.css') }}">
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
            @if($item->is_sold)
                <span class="item-card__sold">Sold</span>
            @endif
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

            <p>☆:{{ $item->likes_count }}  💬:{{ $item->comments_count }}</p>
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
                <p>カテゴリ:
                    @foreach ($item->categories as $category)
                        <span class="item-show__category-badge">{{ $category->name }}</span>
                    @endforeach
                </p>
            @endif
            <p>商品の状態:{{ $item->condition_label }}</p>

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