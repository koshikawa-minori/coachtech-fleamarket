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

        <div class="verify__fake-button">認証はこちらから</div>

        <div class="verify__actions">
            <form method="POST" action="{{ route('verification.send') }}">
            @csrf
                <button type="submit" class="button">認証メールを再送する</button>
            </form>
        </div>
    </div>
</main>
@endsection
