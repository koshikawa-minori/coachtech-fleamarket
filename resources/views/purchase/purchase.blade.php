@extends('layouts.app')

@section('title', '購入確認')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/orders/create.css') }}">
@endpush

@section('content')
<main class="purchase">
  <h1 class="purchase__title">購入確認</h1>

  @if(session('error'))
    <p class="alert alert--error">{{ session('error') }}</p>
  @endif
  @if(session('info'))
    <p class="alert alert--info">{{ session('info') }}</p>
  @endif

  <section class="purchase__item">
    <div class="purchase__thumb">
      @if (filled($item->image_path))
        <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}">
      @else
        <div class="purchase__thumb--placeholder">商品画像</div>
      @endif
    </div>
    <div class="purchase__summary">
      <h2 class="purchase__name">{{ $item->name }}</h2>
      @if(filled($item->brand_name))
        <p class="purchase__brand">{{ $item->brand_name }}</p>
      @endif
      <p class="purchase__price">
        <span class="purchase__currency">¥</span>{{ number_format($item->price) }} <span class="purchase__tax">（税込）</span>
      </p>
      <p class="purchase__seller">出品者：{{ $item->seller?->name ?? '―' }}</p>
    </div>
  </section>

  <section class="purchase__address">
    <h3>お届け先（初期値：プロフィール）</h3>
    <dl class="purchase__address-list">
      <dt>氏名</dt><dd>{{ $authenticatedUser->name }}</dd>
      <dt>郵便番号</dt><dd>{{ $profile->postal_code ?? '未設定' }}</dd>
      <dt>住所</dt><dd>{{ $profile->address ?? '未設定' }}</dd>
      <dt>建物名</dt><dd>{{ $profile->building_name ?? '―' }}</dd>
    </dl>
    {{-- 後で「住所変更画面」へのリンクをここに追加予定 --}}
  </section>

  <section class="purchase__actions">
    <a class="button button--secondary" href="{{ url()->previous() }}">戻る</a>

    @if($item->is_sold)
      <button class="button button--disabled" disabled>購入できません（Sold）</button>
    @else
      <form class="purchase-form" method="post" action="{{ route('purchase.store', ['item_id' => $item->id]) }}">
        @csrf
        <button class="button button--primary" type="submit">購入を確定する（※処理は後で実装）</button>
      </form>
    @endif
  </section>
</main>
@endsection
