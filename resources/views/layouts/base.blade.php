<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @hasSection('admin')
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endif
    @yield('styles')
</head>
<body>
    @yield('content')

    @yield('scripts')
    @stack('scripts')
</body>
</html>
