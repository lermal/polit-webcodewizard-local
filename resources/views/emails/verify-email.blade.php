@extends('emails.layout')

@section('content')
<div class="email-content">
    <h1>Подтверждение email адреса</h1>

    <p>Здравствуйте, {{ $user->name }}!</p>

    <p>Спасибо за регистрацию. Для активации вашего аккаунта, пожалуйста, подтвердите ваш email адрес, нажав на кнопку ниже:</p>

    <div class="button-container">
        <a href="{{ $verificationUrl }}" class="button">
            Подтвердить email адрес
        </a>
    </div>

    <p>Если вы не создавали аккаунт, просто проигнорируйте это письмо.</p>

    <p>С уважением,<br>Команда {{ config('app.name') }}</p>

    <hr>

    <p class="email-subtext">Если у вас не работает кнопка для подтвреждения почты, вы можете попробовать использовать эту ссылку: {{ $verificationUrl }}</p>
</div>
@endsection
