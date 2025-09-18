@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<main class="container">
    <h1>ログイン</h1>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div>
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old ('email') }}" required>
        </div>

        <div>
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <button type="submit">ログインする</button>
    </form>

    <p>
    <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</main>
@endsection
