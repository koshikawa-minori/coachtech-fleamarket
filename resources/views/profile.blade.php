@extends('layouts.app')

@section('title','プロフィール設定')

@section('content')
<main class="profile">
    <h1 class="profile__title">プロフィール設定</h1>

    <form class="profile__form" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="profile__group">
            <div class="profile__image-wrapper">
                @if($profile?->image_path)
                    <img class="profile__image" src="{{ asset('storage/'.$profile->image_path) }}" alt="プロフィール画像">
                @else
                    <div class="profile__image--default"></div>
                @endif
            </div>
            <label class="profile__upload">
                画像を選択する
                <input id="image_path" class="profile__input--file" type="file" name="image_path" accept=".jpeg, .png">
            </label>
            @error('image_path')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="profile__group">
            <label class="profile__label" for="name">ユーザー名</label>
            <input id="name" class="profile__input" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile__group">
            <label class="profile__label" for="postal_code">郵便番号</label>
            <input id="postal_code" class="profile__input" type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
            @error('postal_code')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile__group">
            <label class="profile__label" for="address">住所</label>
            <input id="address" class="profile__input" type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile__group">
            <label class="profile__label" for="building">建物名</label>
            <input id="building" class="profile__input" type="text" name="building" value="{{ old('building', $profile->building ?? '') }}">
            @error('building')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        <button class="profile__button" type="submit">更新する</button>
    </form>
</main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush