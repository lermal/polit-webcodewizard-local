@extends('layouts.info')

@section('title', 'О нас')
@section('qr-link', 'https://ggaek.by/%D0%BA%D0%BE%D0%BB%D0%BB%D0%B5%D0%B4%D0%B6/%D0%B8%D1%81%D1%82%D0%BE%D1%80%D0%B8%D1%8F/')

@section('info-content')
<h1>О Гомельском государственном аграрно-экономическом колледже</h1>

<p>
    Гомельский государственный аграрно-экономический колледж - одно из старейших средних специальных учебных заведений Республики Беларусь.
</p>

<div class="info-cards">
    <div class="info-card card-full">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">История колледжа</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Колледж был основан в 1921 году. За время своего существования подготовил тысячи специалистов для агропромышленного комплекса и других отраслей экономики страны.
                </p>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Специальности</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Правоведение</li>
                    <li>Бухгалтерский учет, анализ и контроль</li>
                    <li>Коммерческая деятельность</li>
                    <li>Программное обеспечение информационных технологий</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Достижения</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Высокий уровень трудоустройства выпускников</li>
                    <li>Современная материально-техническая база</li>
                    <li>Квалифицированный преподавательский состав</li>
                    <li>Активная научная и общественная деятельность</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
