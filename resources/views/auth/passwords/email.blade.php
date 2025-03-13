@extends('layouts.main')

@section('title', 'Восстановление пароля')
@section('navbarTheme', 'navbar-light')

@section('page-content')
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Восстановление пароля</h1>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="auth-button">
                    Отправить ссылку для сброса
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
