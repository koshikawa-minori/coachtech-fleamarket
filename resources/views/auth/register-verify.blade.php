@extends('layouts.app')
@section('title', 'メール認証のご案内')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/register-verify.css')}}">
@endpush

@section('content')
<main class="register-verify">
    <div class="register-verify__group">
        <h1 class="register-verify__title">会員登録が完了しました<br>
        次にメール認証をしてください
        </h1>

        <p class="register-verify__text">
        下のボタンを押すと認証メールが送られます
        </p>

        <form method="POST" action="{{ route('register.verify.send') }}">
        @csrf
            <button type="submit" class="register-verify__button">認証はこちらから</button>
        </form>
    </div>
</main>
@endsection
