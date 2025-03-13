@extends('layouts.info')

@section('title', 'Верховный Суд')
@section('qr-link', 'https://court.gov.by/ru/')

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('power.judicial') }}">Судебная власть</a>
</li>
<li class="breadcrumb-item active">Верховный Суд</li>
@endsection

@section('info-content')
<h1>Верховный Суд</h1>

<p>
    Верховный Суд является высшим судебным органом по гражданским, уголовным, административным и экономическим делам.
</p>

<div class="info-cards">
    <div class="info-card card-full">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">О Верховном Суде</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Верховный Суд возглавляет систему судов общей юрисдикции и осуществляет правосудие путем гражданского, уголовного, административного и экономического судопроизводства. Судьи Верховного Суда назначаются Президентом с согласия Совета Республики.
                    <br><br>
                    В составе Верховного Суда действуют судебные коллегии по гражданским, уголовным, экономическим делам и делам интеллектуальной собственности.
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
                    <li>Рассмотрение дел в качестве суда первой инстанции</li>
                    <li>Рассмотрение дел в кассационном порядке</li>
                    <li>Надзор за судебной деятельностью общих судов</li>
                    <li>Изучение и обобщение судебной практики</li>
                    <li>Разъяснение вопросов применения законодательства</li>
                    <li>Рассмотрение материалов по преступлениям особой тяжести</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Структура</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Пленум Верховного Суда</li>
                    <li>Президиум Верховного Суда</li>
                    <li>Судебная коллегия по гражданским делам</li>
                    <li>Судебная коллегия по уголовным делам</li>
                    <li>Судебная коллегия по экономическим делам</li>
                    <li>Судебная коллегия по делам интеллектуальной собственности</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
