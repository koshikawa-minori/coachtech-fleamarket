@extends('layouts.app')

@section('title', '出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endpush

@section('content')
<main class="sell">
    <h1 class="sell__title">商品の出品</h1>
    <form class="sell__form" method="post" action="{{ route('sell.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- 商品画像 -->
    <section class="sell__section sell-image">
        <h2 class="sell__section-title">商品画像</h2>
        <div class="sell-image__drop">
            <input type="file" name="image" id="image" class="sell-image__input" accept=".jpeg,.jpg,.png">
            <label for="image" class="sell-image__button">画像を選択する</label>
            @error('image')
                <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>
    </section>

    <!-- 商品の詳細（カテゴリ） -->
    <section class="sell__section sell-details">
        <h2 class="sell__section-title">商品の詳細</h2>
        <div class="sell-category">
            <h3 class="sell__subheading">カテゴリー</h3>
            <div class="sell-chips">
                @foreach($categories as $category)
                    <button type="button"
                            class="sell-chip {{ collect(old('category_ids', []))->contains($category->id) ? 'is-selected' : '' }}"
                            data-category-id="{{ $category->id }}">
                    {{ $category->name }}
                    </button>
                @endforeach
                <input type="hidden" name="category_ids[]" id="selectedCategories" value="">
            </div>
            @error('category_ids')
            <p class="alert alert--error">{{ $message }}</p>
            @enderror
        </div>
    </section>

    <!-- 商品の状態（カスタムプルダウン） -->
    <section class="sell__section sell-condition">
        <h2 class="sell__section-title">商品の状態</h2>
        <select id="condition" name="condition" class="form-select custom-select__native">
            <option value="" disabled {{ old('condition') ? '' : 'selected' }}>選択してください</option>
            <option value="1" {{ old('condition')=='1' ? 'selected':'' }}>良好</option>
            <option value="2" {{ old('condition')=='2' ? 'selected':'' }}>目立った傷や汚れなし</option>
            <option value="3" {{ old('condition')=='3' ? 'selected':'' }}>やや傷や汚れあり</option>
            <option value="4" {{ old('condition')=='4' ? 'selected':'' }}>状態が悪い</option>
        </select>
        <div id="custom-condition-select" class="custom-select" hidden></div>

        @error('condition')
        <p class="sell__error">{{ $message }}</p>
        @enderror
    </section>


    <!-- 商品名と説明 -->
    <section class="sell__section sell-basic"> <!-- [追加クラス] sell-basic -->
        <h2 class="sell__section-title">商品名と説明</h2>
        <label class="sell-field">
            <span class="sell-field__label">商品名</span>
            <input type="text" name="name" value="{{ old('name') }}" class="sell-input">
        </label>
        @error('name')
            <p class="sell__error">{{ $message }}</p>
        @enderror

        <label class="sell-field">
            <span class="sell-field__label">ブランド名</span>
            <input type="text" name="brand" value="{{ old('brand') }}" class="sell-input">
        </label>
        @error('brand')
            <p class="sell__error">{{ $message }}</p>
        @enderror

        <label class="sell-field">
            <span class="sell-field__label">商品の説明</span>
            <textarea name="description" rows="5" class="sell-textarea">{{ old('description') }}</textarea>
        </label>
        @error('description')
            <p class="sell__error">{{ $message }}</p>
        @enderror
    </section>

    <!-- 価格 -->
    <section class="sell__section sell-price">
        <h2 class="sell__section-title">販売価格</h2>
        <div class="sell-price__row">
            <span class="sell-price__currency">￥</span>
            <input type="number" name="price" value="{{ old('price') }}" class="sell-input sell-price__input" min="0" step="1" inputmode="numeric">
        </div>
        @error('price')
            <p class="sell__error">{{ $message }}</p>
        @enderror
    </section>


    <!-- 送信 -->
    <div class="sell__actions"> <!-- [追加クラス] sell__actions -->
        <button type="submit" class="button button--primary sell__submit">出品する</button>
    </div>
    </form>
</main>

<!-- 商品状態プルダウン -->
<script>
(function () {
    //変数作り
    const nativeSelectElement = document.getElementById('condition');
    const customSelectContainerElement = document.getElementById('custom-condition-select');
    if (!nativeSelectElement || !customSelectContainerElement) return;

    //ラベル
    const labelByValueMap = {
        '1': '良好',
        '2': '目立った傷や汚れなし',
        '3': 'やや傷や汚れあり',
        '4': '状態が悪い',
    };

    // 初期ラベル
    const currentLabelText = nativeSelectElement.value
        ? (labelByValueMap[nativeSelectElement.value] || '')
        : '選択してください';
    //ラベル名挿入
    customSelectContainerElement.innerHTML = [
        '<button type="button" class="custom-select__trigger" aria-haspopup="listbox" aria-expanded="false">',
            '<span class="custom-select__label">', currentLabelText ,'</span>',
            '<span class="custom-select__caret">▾</span>',
        '</button>',
        '<ul class="custom-select__menu" role="listbox"></ul>'
    ].join('');

    const toggleButtonElement = customSelectContainerElement.querySelector('.custom-select__trigger');
    const optionsMenuElement = customSelectContainerElement.querySelector('.custom-select__menu');

    //同じ項目を <li> 要素へ
    Object.entries(labelByValueMap).forEach(([value, text]) => {
        const optionListItemElement = document.createElement('li');
        optionListItemElement.className = 'custom-select__option';
        optionListItemElement.setAttribute('role', 'option');
        optionListItemElement.dataset.value = value;
        //今選ばれてる項目に✓
        const isSelected = (nativeSelectElement.value === value);
        optionListItemElement.setAttribute('aria-selected', isSelected ? 'true' : 'false');
        // ✓マーク＋表示テキスト
        optionListItemElement.innerHTML = '<span class="custom-select__check">✓</span>' + text;
        optionsMenuElement.appendChild(optionListItemElement);
    });

    //メニュー開閉
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

    //外側がクリックされたら閉じる
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

        //✓更新
        optionsMenuElement.querySelectorAll('.custom-select__option').forEach(function (listItemElement) {
            const isThisSelected = (listItemElement === clickedOptionListItemElement);
            listItemElement.setAttribute('aria-selected', isThisSelected ? 'true' : 'false');
        });

      // メニューを閉じる
        closeCustomSelectMenu();
    });

    // JS有効時：カスタム表示
    nativeSelectElement.classList.add('visually-hidden');
    customSelectContainerElement.hidden = false;
})();
</script>
<!-- 商品状態プルダウン -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    //変数作り
    const chipButtons = document.querySelectorAll('.sell-chip');
    const selectedCategoriesInput = document.getElementById('selectedCategories');
    const selectedIds = new Set();
    // 項目クリックで選択
    chipButtons.forEach(button => {
        button.addEventListener('click', () => {
        const id = button.dataset.categoryId;
        if (selectedIds.has(id)) {
            selectedIds.delete(id);
            button.classList.remove('is-selected');
        } else {
            selectedIds.add(id);
            button.classList.add('is-selected');
        }
        selectedCategoriesInput.value = Array.from(selectedIds);
        });
    });
});
</script>


@endsection
