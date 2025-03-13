@extends('layouts.main')

@section('title', 'Регистрация')
@section('navbarTheme', 'navbar-light')

@section('page-content')
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Регистрация</h1>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
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

                <div class="form-group">
                    <label for="password_confirmation">Подтверждение пароля</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="auth-button">
                    Зарегистрироваться
                    <span class="button-overlay"></span>
                </button>
            </form>

            <div class="auth-links">
                <a href="{{ route('login') }}">
                    У меня уже есть аккаунт
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
