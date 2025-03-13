@extends('layouts.main')

@section('title', 'Вход в личный кабинет')
@section('navbarTheme', 'navbar-light')

@section('page-content')
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Вход в личный кабинет</h1>

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group remember-me">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Запомнить меня
                    </label>
                </div>

                <button type="submit" class="auth-button">
                    Войти
                    <span class="button-overlay"></span>
                </button>
            </form>

            <div class="auth-links">
                <a href="{{ route('register') }}" class="register-link">
                    У меня нет аккаунта
                </a>
                <a href="{{ route('password.request') }}" class="forgot-link">
                    Забыли пароль?
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
