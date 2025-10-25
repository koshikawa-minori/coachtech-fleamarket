@extends('layouts.app')

@section('title', '購入確認')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase/purchase.css') }}">
@endpush

@section('content')
<main class="purchase">
    <form method="post" action="{{ route('purchase.store', $item->id) }}">
        @csrf
        <div class="purchase__top">
            <div class="purchase-left">
                <section class="purchase__item">
                    <div class="purchase__thumb">
                        @if ($item->image_url)
                            <img class="purchase__image" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                        @else
                            <div class="purchase__image--placeholder">商品画像</div>
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

                <section class="purchase__payment">
                    <h2 class="purchase__title">支払い方法</h2>
                    <select id="payment_method" name="payment_method" class="form-select js-native-select">
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
                    <div id="custom-payment-select" class="custom-select" hidden></div>
                    @error('payment_method')
                        <p class="purchase__error">{{ $message }}</p>
                    @enderror
                </section>

                <section class="purchase__address">
                    <div class="purchase__titles">
                        <h2 class="purchase__title">配送先</h2>
                        <a class="button button--secondary" href="{{ route('purchase.edit', $item->id) }}">変更する</a>
                    </div>
                    <div class="purchase__address-list">
                        <p>〒{{ $profile->postal_code ?? '' }}</p>
                        <p>{{ $profile->address ?? '' }}</p>
                        <p>{{ $profile->building ?? '' }}</p>
                    </div>
                    @error('address')
                        <p class="purchase__error">{{ $message }}</p>
                    @enderror
                </section>
            </div>

            <section class="right-summary">
                <table class="purchase__summary-table">
                    <tr>
                        <th>商品代金</th>
                        <td class="purchase__price">
                            <span class="purchase__currency">￥</span>{{ number_format($item->price) }}
                        </td>
                    </tr>
                    <tr>
                        <th>支払い方法</th>
                        <td id="payment_method_preview" class="purchase__note">{{ $paymentLabel }}</td>
                    </tr>
                </table>
                <button class="button button--primary" type="submit">購入する</button>
            </section>
        </div>
    </form>
</main>


<script>
(function () {
    // 変数作り
    const nativeSelectElement = document.getElementById('payment_method');
    if (!nativeSelectElement) return;

    // 右の支払い方法
    const paymentMethodPreviewElement = document.getElementById('payment_method_preview');
    const labelByValueMap = {
        '{{ \App\Models\Order::PAYMENT_CONVENIENCE_STORE_PAYMENT }}': 'コンビニ支払い',
        '{{ \App\Models\Order::PAYMENT_CREDIT_CARD }}': 'カード支払い'
    };

    // 右の支払い方法更新
    function updatePaymentMethodPreview() {
        const selectedValue = nativeSelectElement.value;
        if (!paymentMethodPreviewElement) return;
        paymentMethodPreviewElement.textContent = selectedValue ? (labelByValueMap[selectedValue] || '選択してください') : '選択してください';
    }

    // 表示するラベル決め
    const customSelectContainerElement = document.getElementById('custom-payment-select');
    if (customSelectContainerElement) {
        // 初期ラベル
        const currentLabelText = nativeSelectElement.value
        ? (labelByValueMap[nativeSelectElement.value] || '') : '選択してください';
        // ラベル名挿入
        customSelectContainerElement.innerHTML = [
            '<button type="button" class="custom-select__trigger" aria-haspopup="listbox" aria-expanded="false">',
                '<span class="custom-select__label">', currentLabelText ,'</span>',
                '<span class="custom-select__caret">▾</span>',
            '</button>',
            '<ul class="custom-select__menu" role="listbox"></ul>'
        ].join('');

        const toggleButtonElement = customSelectContainerElement.querySelector('.custom-select__trigger');
        const optionsMenuElement = customSelectContainerElement.querySelector('.custom-select__menu');

        // 同じ項目を <li> 要素へ
        Array.from(nativeSelectElement.options).forEach(function (optionElement) {
            const isPlaceholderOption = (optionElement.value === '');
            if (isPlaceholderOption || optionElement.disabled) return;

            const optionListItemElement = document.createElement('li');
            optionListItemElement.className = 'custom-select__option';
            optionListItemElement.setAttribute('role', 'option');
            optionListItemElement.dataset.value = optionElement.value;
            // 今選ばれてる項目に✓
            const isSelected = nativeSelectElement.value === optionElement.value;
            optionListItemElement.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            // ✓マーク＋表示テキスト
            optionListItemElement.innerHTML =
                '<span class="custom-select__check">✓</span>' + optionElement.textContent;


            optionsMenuElement.appendChild(optionListItemElement);
        });

        // メニュー開閉
        function openCustomSelectMenu() {
            customSelectContainerElement.classList.add('is-open');
            toggleButtonElement.setAttribute('aria-expanded', 'true');
        }
        function closeCustomSelectMenu() {
            customSelectContainerElement.classList.remove('is-open');
            toggleButtonElement.setAttribute('aria-expanded', 'false');
        }
        toggleButtonElement.addEventListener('click', function () {
            if (customSelectContainerElement.classList.contains('is-open')) {
                closeCustomSelectMenu();
            } else {
                openCustomSelectMenu();
            }
        });

        // 外側がクリックされたら閉じる
        document.addEventListener('click', function (event) {
            if (!customSelectContainerElement.contains(event.target)) {
                closeCustomSelectMenu();
            }
        });
        // 項目クリックで選択
        optionsMenuElement.addEventListener('click', function (event) {
            const clickedOptionListItemElement = event.target.closest('.custom-select__option');
            if (!clickedOptionListItemElement) return;

            const newSelectedValue = clickedOptionListItemElement.dataset.value;

            nativeSelectElement.value = newSelectedValue;

            // トリガーのラベルを更新
            const labelSpanElement = toggleButtonElement.querySelector('.custom-select__label');
            labelSpanElement.textContent = labelByValueMap[newSelectedValue] || '';

            // ✓更新
            optionsMenuElement.querySelectorAll('.custom-select__option').forEach(function (listItemElement) {
                const isThisSelected = (listItemElement === clickedOptionListItemElement);
                listItemElement.setAttribute('aria-selected', isThisSelected ? 'true' : 'false');
            });

            // 右側プレビューも更新
            updatePaymentMethodPreview();

            // メニューを閉じる
            closeCustomSelectMenu();
        });

        // JS有効時：カスタム表示
        nativeSelectElement.classList.add('visually-hidden');
        customSelectContainerElement.hidden = false;

    }
        nativeSelectElement.addEventListener('change', updatePaymentMethodPreview);

        // --- 初期表示の右プレビュー反映 ---
        updatePaymentMethodPreview();
})();
</script>

@endsection