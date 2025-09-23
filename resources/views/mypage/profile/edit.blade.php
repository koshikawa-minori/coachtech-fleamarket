@extends('layouts.app')

@section('content')
<h1>プロフィール編集</h1>

@if ($mode === 'first')
    <p>初回設定です。必要な項目を入力して保存してください。</p>
@endif

@include('mypage.profile._form')
@endsection
