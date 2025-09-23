@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<main class="login">
    <h1>ログイン</h1>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="login__group">
            <label class="login__label" for="email">メールアドレス</label>
            <input id="email" class="login__input" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <p class="login__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="login__group">
            <label class="login__label" for="password">パスワード</label>
            <input id="password" class="login__input" type="password" name="password" required>
            @error('password')
                <p class="login__error">{{ $message }}</p>
            @enderror
        </div>

        <button class="login__button" type="submit">ログインする</button>
    </form>

    <p class="login__link">
    <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</main>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css')}}">
@endpush
