@extends('layouts.info')

@section('title', 'Контакты')
@section('qr-link', 'https://ggaek.by/contacts')

@section('info-content')
<h1>Контактная информация</h1>

<p>
    Гомельский государственный аграрно-экономический колледж всегда открыт для связи с абитуриентами, студентами и партнерами.
</p>

<div class="info-cards">
    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Адрес и телефоны</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Адрес: 246017 г. Гомель, ул. Пролетарская, 39</li>
                    <li>Приемная: +375 (232) 50-05-71</li>
                    <li>Приемная комиссия: +375 (232) 50-05-71</li>
                    <li>Email: ggaek@post.gomel.by</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Режим работы</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Понедельник - Пятница: 8:00 - 17:00</li>
                    <li>Суббота: 8:00 - 13:00</li>
                    <li>Воскресенье: выходной</li>
                    <li>Обед: 13:00 - 14:00</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-full">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Как добраться</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Колледж расположен в центре города. Добраться можно:
                </p>
                <ul class="info-card-list">
                    <li>Автобусы: 3, 7, 7а, 8, 12, 19, 20</li>
                    <li>Троллейбусы: 1, 15, 20</li>
                    <li>Маршрутные такси: 1-т, 3-т, 4-т</li>
                    <li>Остановка: "Колледж"</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
