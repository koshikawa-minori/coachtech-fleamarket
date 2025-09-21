@extends('layouts.app')

@section('title', '商品一覧')
@section('content')

    <nav class="tab-menu">
        <a>おすすめ</a>
        <a>マイリスト</a>
    </nav>

    <ul>
        @foreach ($items as $item)
        <li>
            <img src="{{ $item->image_path }}" alt="{{ $item->name}}">
            <p>{{ $item->name }}</p>
        </li>
        @endforeach
    </ul>
@endsection
