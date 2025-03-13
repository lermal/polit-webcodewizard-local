@extends('layouts.info')

@section('title', 'Совет Республики')
@section('qr-link', 'http://www.sovrep.gov.by')

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('power.legislative') }}">Законодательная власть</a>
</li>
<li class="breadcrumb-item active">Совет Республики</li>
@endsection

@section('info-content')
<h1>Совет Республики</h1>

<p>
    Совет Республики является верхней палатой Парламента Республики Беларусь – палатой территориального представительства.
</p>

<div class="info-cards">
    <!-- Карточка на всю ширину -->
    <div class="info-card card-full card-council">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">О Совете Республики</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Совет Республики является палатой территориального представительства. От каждой области и города Минска тайным голосованием избираются по восемь членов Совета Республики. Восемь членов назначаются Президентом Республики Беларусь.
                    <br><br>
                    Членом Совета Республики может быть гражданин Республики Беларусь, достигший 30 лет и проживший на территории соответствующей области не менее пяти лет.
                </p>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-powers">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Полномочия</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Одобрение или отклонение законов, принятых Палатой представителей</li>
                    <li>Отмена решений местных Советов депутатов</li>
                    <li>Избрание шести судей Конституционного Суда</li>
                    <li>Дача согласия на назначение Председателя Конституционного Суда</li>
                    <li>Участие в назначении Председателя Центральной комиссии по выборам</li>
                    <li>Рассмотрение указов Президента о введении чрезвычайного положения</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-structure">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Структура</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Председатель Совета Республики</li>
                    <li>Заместитель Председателя</li>
                    <li>Президиум Совета Республики</li>
                    <li>Постоянные комиссии</li>
                    <li>Секретариат Совета Республики</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
