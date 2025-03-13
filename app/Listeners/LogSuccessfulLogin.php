<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Carbon\Carbon;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        // Принудительно устанавливаем московское время
        $moscowTime = Carbon::now()->timezone('Europe/Moscow');

        // Обновляем напрямую в БД, минуя преобразования модели
        \DB::table('users')
            ->where('id', $event->user->id)
            ->update(['last_login' => $moscowTime]);
    }
}
