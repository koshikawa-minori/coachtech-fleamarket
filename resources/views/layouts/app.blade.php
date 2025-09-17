<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Coachtechフリマ')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <a href="{{ url('/') }}" class="header__logo" >
                    <img src="{{ asset('images/logo.svg')}}" alt="COACHTECHロゴ">
                </a>
            </div>

            <div class="header__center">
                <form class="header__search" action="{{ url('/') }}" method="GET">
                <input class="header__search-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                <!--一応つけてる検索ボタン コーチ返信待ち -->
                <button type="submit" class="header__search-button">検索</button>
                </form>
            </div>

            <div class="header__right">
                @auth
                <form class="header__logout" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="header__button">ログアウト</button>
                </form>
                <a class="header__button" href="{{ route('mypage', [], false) }}">マイページ</a>
                <a class="header__button header__button--primary" href="{{ route('sell', [], false) }}">出品</a>
                    @else
                <a class="header__button" href="{{ route('login') }}">ログイン</a>
                <a class="header__button" href="{{ route('mypage', [], false) }}">マイページ</a>
                <a class="header__button header__button--primary" href="{{ route('sell', [], false) }}">出品</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>

</body>
</html>
