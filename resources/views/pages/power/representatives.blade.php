@extends('layouts.info')

@section('title', 'Палата представителей')
@section('qr-link', 'https://president.gov.by/ru/statebodies/konstitucionnyy-sud-respubliki-belarus')

@section('info-content')
<h1>Палата представителей</h1>

<p>
    Палата представителей является нижней палатой Парламента Республики Беларусь, избираемой непосредственно народом.
</p>

<div class="info-cards">
    <!-- Карточка на всю ширину -->
    <div class="info-card card-full card-representatives">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">О Палате представителей</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Палата представителей состоит из 110 депутатов, избираемых на основе всеобщего, свободного, равного, прямого избирательного права при тайном голосовании. Срок полномочий Палаты представителей – 4 года.
                    <br><br>
                    Депутатом может быть гражданин Республики Беларусь, достигший 21 года. Одно и то же лицо не может одновременно являться членом двух палат Парламента.
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
                    <li>Рассмотрение проектов законов</li>
                    <li>Назначение выборов Президента</li>
                    <li>Дача согласия на назначение Премьер-министра</li>
                    <li>Рассмотрение вопроса о доверии Правительству</li>
                    <li>Заслушивание доклада Премьер-министра о программе деятельности Правительства</li>
                    <li>Рассмотрение отчетов Правительства</li>
                    <li>Объявление амнистии</li>
                    <li>Выдвижение обвинения против Президента</li>
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
                    <li>Председатель Палаты представителей</li>
                    <li>Заместитель Председателя</li>
                    <li>Совет Палаты представителей</li>
                    <li>Постоянные комиссии</li>
                    <li>Секретариат Палаты представителей</li>
                    <li>Депутатские группы</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('power.legislative') }}">Законодательная власть</a>
</li>
<li class="breadcrumb-item active">Палата представителей</li>
@endsection
@endsection
