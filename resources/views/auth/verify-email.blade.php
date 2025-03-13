@extends('layouts.main')

@section('title', 'Подтверждение email')
@section('navbarTheme', 'navbar-light')

@section('page-content')
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1 style="margin-bottom: 0;">Подтверждение email</h1>

            @if (session('status'))
                <div class="alert alert-success" role="alert" style="margin-bottom: 16px;">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning" role="alert" style="margin-bottom: 16px;">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="auth-message" style="margin-bottom: 16px;">
                Спасибо за регистрацию! Прежде чем начать, не могли бы вы подтвердить свой email, перейдя по ссылке, которую мы только что отправили вам? Если вы не получили письмо, мы с радостью отправим вам другое.
            </div>

            <form method="POST" action="{{ route('verification.send') }}" class="auth-form">
                @csrf
                <button type="submit" class="auth-button">
                    Отправить письмо повторно
                    <span class="button-overlay"></span>
                </button>
            </form>

            <div class="auth-links">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="link-button">
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
