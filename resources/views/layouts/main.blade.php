@extends('layouts.base')

@section('content')
    <head>
        @stack('head')
    </head>
    <div class="wrapper">
        <header class="navbar {{ $navbarTheme ?? 'navbar-dark' }}" id="navbar">
            <div class="header-container">
                <div class="header-top">
                    <a href="{{ route('home') }}" class="logo">
                        Виртуальный <span class="logo-accent">правовой</span> портал
                    </a>

                    <button class="mobile-menu-button">
                        <svg class="menu-icon" viewBox="0 0 20 20">
                            <path class="menu-open" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"></path>
                            <path class="menu-close" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path>
                        </svg>
                    </button>
                </div>

                <nav class="main-nav" id="mainNav">
                    <a href="{{ route('home') }}" class="nav-link">Главная</a>
                    <a href="{{ route('about') }}" class="nav-link">О нас</a>
                    <a href="{{ route('contacts') }}" class="nav-link">Контакты</a>
                    <a href="{{ route('maps') }}" class="nav-link">Карта</a>
                    @guest
                        <a href="{{ route('login') }}" class="nav-button">
                            Личный кабинет
                            <span class="button-overlay"></span>
                        </a>
                    @else
                        <a href="{{ route('admin.stats') }}" class="nav-button">
                            Личный кабинет
                            <span class="button-overlay"></span>
                        </a>
                    @endguest
                </nav>
            </div>
        </header>

        <main class="main @yield('body-class')">
            @yield('page-content')
        </main>

        <footer class="footer">
        </footer>
    </div>
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@endsection
