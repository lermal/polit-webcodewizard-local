@extends('layouts.info')

@section('title', 'Исполнительная власть')
@section('qr-link', 'https://president.gov.by/ru/gosudarstvo/ustrojstvo/ispolnitelnaja')
@section('breadcrumbs')
<li class="breadcrumb-item active">Исполнительная власть</li>
@endsection

@section('info-content')
<h1>Исполнительная власть</h1>

<p>
    Исполнительную власть в Республике Беларусь осуществляет Правительство – Совет Министров Республики Беларусь.
</p>

<div class="info-cards">
    <!-- Карточка на всю ширину -->
    <div class="info-card card-full">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">О Правительстве</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Правительство является центральным органом государственного управления. В своей деятельности оно подотчетно Президенту и ответственно перед Парламентом. Правительство слагает свои полномочия перед вновь избранным Президентом.
                    <br><br>
                    Премьер-министр назначается Президентом с согласия Палаты представителей. Решение принимается не позднее чем в двухнедельный срок со дня внесения предложения по кандидатуре.
                </p>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Полномочия</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Руководство системой подчиненных органов</li>
                    <li>Разработка основных направлений внутренней и внешней политики</li>
                    <li>Разработка и исполнение бюджета</li>
                    <li>Обеспечение проведения единой экономической политики</li>
                    <li>Регулирование деятельности всех отраслей экономики</li>
                    <li>Принятие мер по обеспечению прав граждан</li>
                    <li>Обеспечение исполнения Конституции и законов</li>
                    <li>Организация внешнеэкономической деятельности</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Состав</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Премьер-министр</li>
                    <li>Заместители Премьер-министра</li>
                    <li>Министры</li>
                    <li>Председатели государственных комитетов</li>
                    <li>Руководители иных республиканских органов управления</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Министерства</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Министерство иностранных дел</li>
                    <li>Министерство внутренних дел</li>
                    <li>Министерство обороны</li>
                    <li>Министерство финансов</li>
                    <li>Министерство экономики</li>
                    <li>Министерство образования</li>
                    <li>Министерство здравоохранения</li>
                    <li>И другие отраслевые министерства</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Деятельность</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Издание постановлений и распоряжений</li>
                    <li>Разработка программ развития</li>
                    <li>Координация работы госорганов</li>
                    <li>Управление государственной собственностью</li>
                    <li>Обеспечение национальной безопасности</li>
                    <li>Реализация социальной политики</li>
                    <li>Развитие международного сотрудничества</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
