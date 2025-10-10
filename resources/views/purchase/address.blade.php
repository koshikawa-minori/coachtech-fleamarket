@extends('layouts.app')

@section('title', '住所')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}">
@endpush

@section('content')
<main class="address">
    <h1 class="address__title">住所の変更</h1>

    <form class="address__form" action="{{ route('purchase.update', $item->id) }}" method="post"  novalidate>
        @csrf
        <div class="address__group">
            <label class="address__label" for="postal_code">郵便番号</label>
            <input id="postal_code" class="address__input" type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
            @error('postal_code')
                <p class="address__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="address__group">
            <label class="address__label" for="address">住所</label>
            <input id="address" class="address__input" type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
                <p class="address__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="address__group">
            <label class="address__label" for="building">建物名</label>
            <input id="building" class="address__input" type="text" name="building" value="{{ old('building', $profile->building ?? '') }}">
            @error('building')
                <p class="address__error">{{ $message }}</p>
            @enderror
        </div>

        <button class="address__button" type="submit">更新する</button>
    </form>
</main>
@endsection

