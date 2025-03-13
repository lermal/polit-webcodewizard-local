@extends('layouts.main')

@section('title', 'Сброс пароля')
@section('navbarTheme', 'navbar-light')

@section('page-content')
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Сброс пароля</h1>

            <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? request()->query('email') }}">

                <div class="form-group">
                    <label for="password">Новый пароль</label>
                    <input type="password" id="password" name="password" required autofocus>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">Подтвердите пароль</label>
                    <input type="password" id="password-confirm" name="password_confirmation" required>
                </div>

                <button type="submit" class="auth-button">
                    Сбросить пароль
                    <span class="button-overlay"></span>
                </button>
            </form>

            <div class="auth-links">
                <a href="{{ route('login') }}">
                    Вернуться к входу
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
