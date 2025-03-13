@extends('layouts.main')

@section('title', 'Email подтвержден')
@section('navbarTheme', 'navbar-light')

@section('page-content')
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1 style="margin-bottom: 0;">Email подтвержден!</h1>

            <div class="auth-message" style="margin: 0; margin-bottom: 16px;">
                Спасибо за подтверждение вашего email адреса. Теперь вы можете войти в свой аккаунт и получить доступ ко всем функциям платформы.
            </div>

            <div class="auth-links">
                <a href="{{ route('login') }}" style="text-decoration: none; text-align: center; display: block; width: 100%;">
                    Войти в аккаунт
                    <span class="button-overlay"></span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
