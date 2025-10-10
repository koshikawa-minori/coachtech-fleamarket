@extends('layouts.app')

@section('title', '購入確認')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase/purchase.css') }}">
@endpush

@section('content')
<main class="purchase">
  @if(session('error'))
    <p class="alert alert--error">{{ session('error') }}</p>
  @endif
  @if(session('info'))
    <p class="alert alert--info">{{ session('info') }}</p>
  @endif

  <section class="purchase__item">
    <div class="purchase__thumb">
      @php
        use Illuminate\Support\Str;
        $src = Str::startsWith($item->image_path, ['http://','https://'])
            ? $item->image_path
            : asset($item->image_path);
      @endphp

      @if (filled($item->image_path))
        <img src="{{ $src }}" alt="{{ $item->name }}">
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
        <span class="purchase__currency">¥</span>{{ number_format($item->price) }}
        <span class="purchase__tax">（税込）</span>
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
      <dt>建物名</dt><dd>{{ $profile->building ?? '―' }}</dd>
    </dl>

    {{-- いつでも住所変更できる導線（要件①の遷移を保証） --}}
    <div class="purchase__address-actions">
      <a class="button button--secondary" href="{{ route('purchase.edit', $item->id) }}">配送先を変更する</a>
    </div>
  </section>

  @php
    $isAddressReady = filled($profile->postal_code) && filled($profile->address);
  @endphp

  <section class="purchase__actions">
    <a class="button button--ghost" href="{{ url()->previous() }}">戻る</a>

    @if(!$isAddressReady)
      {{-- 未設定時は住所変更画面へ誘導 --}}
      <a class="button button--primary" href="{{ route('purchase.edit', $item->id) }}">住所を設定してから購入へ</a>
    @elseif($item->is_sold)
      <button class="button button--disabled" disabled>購入できません（Sold）</button>
    @else
      <form class="purchase-form" method="post" action="{{ route('purchase.store', $item->id) }}">
        @csrf
        {{-- PurchaseRequest 通過用（プロフィールの値をそのまま送る） --}}
        <input type="hidden" name="postal_code" value="{{ $profile->postal_code }}">
        <input type="hidden" name="address" value="{{ $profile->address }}">
        <input type="hidden" name="building" value="{{ $profile->building ?? '' }}">
        {{-- ★ 重複を解消：どちらか一方に。今は数値 1 を採用 --}}
        <input type="hidden" name="payment_method" value="1">

        <button class="button button--primary" type="submit">購入を確定する</button>
      </form>
    @endif
  </section>
</main>
@endsection
