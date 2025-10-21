@extends('layouts.app')
@section('title', 'メール認証')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/verify.css')}}">
@endpush

@section('content')
<main class="verify">
    <div class="verify__group">
        <div class="verify__title">
            <p class="verify__text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
            </p>
        </div>

        <div class="verify-guide__actions">
            <a class="button" href="{{ route('verification.notice') }}">認証はこちらから</a>

            <form method="POST" action="{{ route('verification.send') }}">
            @csrf
                <button type="submit" class="button verify--secondary">認証メールを再送する</button>
            </form>
        </div>
        @if (session('status') === 'verification-link-sent')
            <p class="verify-guide__notice">認証メールを再送しました。数分お待ちください。</p>
        @endif
    </div>
</main>
@endsection
