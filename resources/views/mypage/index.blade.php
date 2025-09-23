@extends('layouts.app')

@section('content')
<h1>プロフィール</h1>

@if (session('success'))
    <p class="success">{{ session('success') }}</p>
@endif

<p>ユーザー名：{{ $user->name }}</p>
<p>郵便番号：{{ $profile->postal_code ?? '未登録' }}</p>
<p>住所：{{ $profile->address ?? '未登録' }}</p>

@if($profile?->image_path)
    <div style="margin-top:8px;">
        <img src="{{ asset('storage/' . $profile->image_path) }}" alt="プロフィール画像" style="max-width:160px;">
    </div>
@endif

<p style="margin-top:16px;"><a href="{{ route('profile.edit') }}">編集する</a></p>
@endsection
