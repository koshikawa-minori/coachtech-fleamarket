@extends('layouts.app')

@section('title', '購入確認')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase/purchase.css') }}">
@endpush

@section('content')
<main class="purchase">
    <section class="purchase__item">
        <div class="purchase__thumb">
            @if ($item->image_url)
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
            @else
                <div class="purchase__thumb--placeholder">商品画像</div>
            @endif
        </div>

        <div class="purchase-detail">
            <h2 class="purchase__name">{{ $item->name }}</h2>
            <p class="purchase__price">
                <span class="purchase__currency">￥</span>
                <span class="purchase__price-value">{{ number_format($item->price) }}</span>
            </p>
        </div>
    </section>

    <h2 class="purchase-title">支払い方法</h2>

    @if ($item->is_sold)
        <button class="button button--disabled" disabled>売り切れ</button>

    @elseif (!$isAddressReady)
    <a class="button button--primary" href="{{ route('purchase.edit', $item->id) }}">住所を設定してから購入へ</a>

    @else
        <form class="purchase-form" method="post" action="{{ route('purchase.store', $item->id) }}">
            @csrf
            {{-- 支払い方法（プルダウン） --}}
            <label for="payment_method" class="form-label">支払い方法</label>
            <select id="payment_method" name="payment_method" class="form-select">
                <option value="" disabled {{ $selectedPayment ? '' : 'selected' }}>選択してください</option>
                <option value="{{ \App\Models\Order::PAYMENT_CONVENIENCE_STORE_PAYMENT }}"
                    {{ (string)$selectedPayment === (string)\App\Models\Order::PAYMENT_CONVENIENCE_STORE_PAYMENT ? 'selected' : '' }}>
                    コンビニ支払い
                </option>
                <option value="{{ \App\Models\Order::PAYMENT_CREDIT_CARD }}"
                    {{ (string)$selectedPayment === (string)\App\Models\Order::PAYMENT_CREDIT_CARD ? 'selected' : '' }}>
                    カード支払い
                </option>
            </select>
            @error('payment_method')
                <p class="alert alert--error">{{ $message }}</p>
            @enderror

            {{-- PurchaseRequest 通過用（プロフィール値を送る） --}}
            <input type="hidden" name="postal_code" value="{{ $profile->postal_code }}">
            <input type="hidden" name="address" value="{{ $profile->address }}">
            <input type="hidden" name="building" value="{{ $profile->building ?? '' }}">

            <section class="purchase__address">
                <h2 class="purchase-title">配送先</h2>
                <div class="purchase__address-list">
                    <p>〒{{ $profile->postal_code ?? '' }}</p>
                    <p>{{ $profile->address ?? '' }}</p>
                    <p>{{ $profile->building ?? '' }}</p>
                </div>
                <div class="purchase__address-actions">
                    <a class="button button--secondary" href="{{ route('purchase.edit', $item->id) }}">変更する</a>
                </div>
            </section>

            {{-- 右カラムのサマリー --}}
            <table class="purchase__summary-table">
                <tr>
                    <th>商品代金</th>
                    <td class="purchase__price">
                        <span class="purchase__currency">￥</span>{{ number_format($item->price) }}
                    </td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    {{-- JSが無効でもサーバ側の$paymentLabelが表示されるフォールバック --}}
                    <td id="payment_method_preview" class="purchase__note">{{ $paymentLabel }}</td>
                </tr>
            </table>

            <button class="button button--primary" type="submit">購入する</button>
        </form>
    @endif
</main>

{{-- JS：選択変更で右サマリー即時更新（表示のみ変更） --}}
<script>
(function () {
    var select = document.getElementById('payment_method');
    if (!select) return;
    var preview = document.getElementById('payment_method_preview');
    var LABELS = {
    '{{ \App\Models\Order::PAYMENT_CONVENIENCE_STORE_PAYMENT }}': 'コンビニ支払い',
    '{{ \App\Models\Order::PAYMENT_CREDIT_CARD }}': 'カード支払い'
    };

    function update() {
    var v = select.value;
    preview.textContent = v ? (LABELS[v] || '選択してください') : '選択してください';
    }

    select.addEventListener('change', update);
  update(); // 初期表示（old()の反映 or プレースホルダ）
})();
</script>
@endsection