@extends('layouts.app')
@section('title', 'メール認証')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/verify.css')}}">
@endpush

@section('content')
<main class="verify">
    <div class="verify__group">
        <h1 class="verify__title">登録していただいたメールアドレスに<br>認証メールを送付しました</h1>
        <p class="verify__text">
        メール内のリンクをクリックしてください<br>
        認証が完了するとプロフィール設定画面が開きます
        </p>

        <div class="verify__actions">
            <form method="POST" action="{{ route('verification.send') }}">
            @csrf
                <button type="submit" class="verify__button">認証メールを再送する</button>
            </form>
        </div>
    </div>
</main>
@endsection
