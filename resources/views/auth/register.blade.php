@extends('layouts.app')

@section('title','会員登録')

@section ('content')
<main class="register">
    <h1>会員登録</h1>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div class="register__group">
            <label class="register__label" for="name">ユーザー名</label>
            <input id="name" class="register__input" type="text" name="name" value="{{ old('name') }}" required>
            @error('name')
                <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="register__group">
            <label class="register__label" for="email">メールアドレス</label>
            <input id="email" class="register__input" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="register__group">
            <label class="register__label" for="password">パスワード</label>
            <input id="password" class="register__input" type="password" name="password" required>
            @error('password')
                <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="register__group">
            <label class="register__label" for="password_confirmation">確認用パスワード</label>
            <input id="password_confirmation" class="register__input" type="password" name="password_confirmation" required>
            @error('password_confirmation')
                <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        <button class="register__button" type="submit">登録する</button>

    </form>

    <p class="register__link">
    <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
</main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endpush