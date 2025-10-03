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
        </a>

        <div class="item-detail">
            <h1 class="item-show__title">{{ $item->name }}</h1>
            <h3>ブランド名</h3>
            <p>￥</p>
            <p>{{ number_format($item->price) }}(税込)</p>

            <p>☆:{{ $item->likes_count }}  💬:{{ $item->comments_count }}</p>
            <button>購入手続きへ</button>

            <h2>商品説明</h2>
            <!-- カラー  状態  コメント 表示-->

            <h2>商品の情報</h2>
            @if ($item->categories->isNotEmpty())
                <p>カテゴリ:
                    @foreach ($item->categories as $category)
                        <span>{{ $category->name }}</span>
                    @endforeach
                </p>
            @endif
            <p>商品の状態:{{ $item->condition_label }}</p>


            <h2 class="comment-title">コメント:{{ $item->comments_count }}</h2>
            <section class="comments">
                @foreach ($comments as $comment)
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
                        <!-- コーチに確認中-->
                        <p class="comment__body">{{ $comment->comment }}</p>
                    </div>
                @endforeach
            </section>

            <h2 class="item-detail__title">商品へのコメント</h2>
            <!-- コーチに確認中
                                    入力欄 -->
            @auth
            <form class="comment-form" method="post" action="{{ route('comments.store', ['item_id' => $item->id]) }}">
                @csrf
                <textarea class="comment-form__textarea" name="comment">{{ old('comment') }}</textarea>
                @error('comment')
                <p class="comment--error">{{ $message }}</p>
                @enderror

                <button>コメントを送信する</button>
            </form>
            @endauth
        </div>
    </div>
</main>
@endsection