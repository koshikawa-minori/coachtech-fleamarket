@extends('layouts.app')
@section('title', '登録完了のご案内')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/register-verify.css')}}">
@endpush

@section('content')
<main class="register-verify">
    <div class="register-verify__group">
        <h1 class="register-verify__title">会員登録が完了しました。<br>
        認証メールを送信しました。
        </h1>

        <p class="register-verify__text">
        メールに記載されたリンクを開いて認証を完了してください。<br>
        メールが届かない場合は<br>
        下のボタンから案内ページへお進みください。
        </p>
        <a href="{{ route('verification.notice') }}" class="register-verify__button">
        認証はこちらから
        </a>
    </div>
</main>
@endsection
