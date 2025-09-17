@extends('layouts.app')

@section('title','会員登録')

@section ('content')
<main class="container">
    <h1>会員登録</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old ('name') }}" required>
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old ('email') }}" required>
        </div>

        <div>
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div>
            <label for="password_confirmation">確認用パスワード</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit">登録する</button>
    </form>

    <p>
    <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
</main>
@endsection
