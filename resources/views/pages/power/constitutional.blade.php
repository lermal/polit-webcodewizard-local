@extends('layouts.info')

@section('title', 'Конституционный Суд')
@section('qr-link', 'http://www.kc.gov.by')

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('power.judicial') }}">Судебная власть</a>
</li>
<li class="breadcrumb-item active">Конституционный Суд</li>
@endsection

@section('info-content')
<h1>Конституционный Суд</h1>

<p>
    Конституционный Суд Республики Беларусь – орган судебного контроля за конституционностью нормативных правовых актов в государстве.
</p>

<div class="info-cards">
    <div class="info-card card-full">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">О Конституционном Суде</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Конституционный Суд формируется в количестве 12 судей из высококвалифицированных специалистов в области права. Шесть судей назначаются Президентом Республики Беларусь, шесть судей избираются Советом Республики. Председатель Конституционного Суда назначается Президентом с согласия Совета Республики.
                    <br><br>
                    Судьи Конституционного Суда избираются или назначаются на 11 лет и могут быть освобождены от должности только по основаниям, предусмотренным законом.
                </p>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Полномочия</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Проверка конституционности нормативных правовых актов</li>
                    <li>Дача заключений о соответствии законов Конституции</li>
                    <li>Разрешение споров о компетенции между государственными органами</li>
                    <li>Толкование Конституции</li>
                    <li>Проверка конституционности международных договоров</li>
                    <li>Рассмотрение вопросов о конституционности выборов</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Принципы деятельности</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Верховенство Конституции</li>
                    <li>Независимость</li>
                    <li>Коллегиальность</li>
                    <li>Гласность</li>
                    <li>Равенство сторон</li>
                    <li>Состязательность процесса</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
