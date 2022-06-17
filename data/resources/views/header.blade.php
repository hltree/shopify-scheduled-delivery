<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('dark.css') }}" rel="stylesheet" />
    <script src="{{ asset('sweetalert2.min.js') }}"></script>
    <link href="{{ asset('style.css') }}" rel="stylesheet" />
    <style>
        .float-nav {
            position: fixed;
            right: 20px;
            top: 20px;
        }

        .float-nav ul {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0;
            width: 80px;
        }

        .float-nav ul li {
            list-style: none;
        }
        .float-nav .setting {
            background: url('{{ asset('setting.png') }}') no-repeat center/contain;
            display: block;
            font-size: 0;
            line-height: 0;
            letter-spacing: 0;
            text-indent: 0;
            height: 32px;
            width: 32px;
        }

        .float-nav .home {
            background: url('{{ asset('home.png') }}') no-repeat center/contain;
            display: block;
            font-size: 0;
            line-height: 0;
            letter-spacing: 0;
            text-indent: 0;
            height: 32px;
            opacity: .6;
            width: 32px;
        }
    </style>
</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 sm:items-center py-4 sm:pt-0">
    <nav class="float-nav">
        <ul>
            <li><a href="{{ route('home') }}" class="home">家</a></li>
            <li><a href="{{ route('setting.index') }}" class="setting">設定</a></li>
        </ul>
    </nav>
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            @php
                $current = \Illuminate\Support\Facades\Route::current();
            @endphp
            <a href="{{ route('home') }}" class="btn btn-secondary {{ 'home' === $current->getName() ? 'active' : '' }}">初期設定</a>
            @if ($installed)
                <a href="{{ route('setting.index') }}" class="btn btn-secondary {{ 'setting' === explode('.', $current->getName())[0] ? 'active' : '' }}">配送希望日時カレンダーの設定</a>
                <a href="{{ route('csv.index') }}" class="btn btn-secondary {{ 'csv' === explode('.', $current->getName())[0] ? 'active' : '' }}">CSV出力</a>
            @endif
        </div>
