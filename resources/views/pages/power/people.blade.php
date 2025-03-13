@extends('layouts.info')

@section('title', 'Народ Республики Беларусь')
@section('qr-link', 'https://president.gov.by/ru/gosudarstvo/constitution')

@section('breadcrumbs')
<li class="breadcrumb-item active">Народ Республики Беларусь</li>
@endsection

@section('info-content')
<h1>Народ Республики Беларусь</h1>

<p>
    Единственным источником государственной власти и носителем суверенитета в Республике Беларусь является народ.
</p>

<div class="info-cards">
    <!-- Карточка на всю ширину -->
    <div class="info-card card-full">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Народовластие</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Народ осуществляет свою власть непосредственно через референдумы и свободные выборы, а также через государственные органы. Граждане Республики Беларусь имеют право участвовать в решении государственных дел как непосредственно, так и через свободно избранных представителей.
                    <br><br>
                    Любые действия по изменению конституционного строя и достижению государственной власти насильственными методами, а также путем иного нарушения законов Республики Беларусь наказываются согласно закону.
                </p>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Формы народовластия</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Референдумы республиканские и местные</li>
                    <li>Выборы Президента</li>
                    <li>Выборы депутатов Палаты представителей</li>
                    <li>Выборы членов Совета Республики</li>
                    <li>Выборы депутатов местных Советов</li>
                    <li>Всебелорусское народное собрание</li>
                    <li>Местное самоуправление</li>
                    <li>Общественные обсуждения</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Конституционные права</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Право избирать и быть избранным</li>
                    <li>Право на участие в референдумах</li>
                    <li>Право на обращения в госорганы</li>
                    <li>Право на объединение в партии и организации</li>
                    <li>Право на мирные собрания</li>
                    <li>Право на получение информации о деятельности госорганов</li>
                    <li>Право на судебную защиту</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Гражданские инициативы</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Законодательная инициатива (не менее 50 тысяч граждан)</li>
                    <li>Инициатива проведения референдума</li>
                    <li>Отзыв депутата</li>
                    <li>Общественные обсуждения законопроектов</li>
                    <li>Местные инициативы</li>
                    <li>Обращения в государственные органы</li>
                    <li>Общественный контроль</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Гарантии народовластия</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Верховенство права</li>
                    <li>Разделение властей</li>
                    <li>Независимость судов</li>
                    <li>Свобода СМИ</li>
                    <li>Политический плюрализм</li>
                    <li>Идеологическое многообразие</li>
                    <li>Местное самоуправление</li>
                    <li>Общественный контроль</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
