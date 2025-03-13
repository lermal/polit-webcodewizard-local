@extends('layouts.info')

@section('title', 'Судебная власть')
@section('qr-link', 'http://www.court.gov.by')

@section('breadcrumbs')
<li class="breadcrumb-item active">Судебная власть</li>
@endsection

@section('info-content')
<h1>Судебная власть</h1>

<p>
    Судебная власть в Республике Беларусь принадлежит судам. Система судов строится на принципах территориальности и специализации.
</p>

<div class="info-cards">
    <!-- Карточка на всю ширину -->
    <div class="info-card card-full card-judicial">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">О судебной системе</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Судебная власть осуществляется посредством конституционного, гражданского, уголовного, административного и хозяйственного судопроизводства. Судьи при осуществлении правосудия независимы и подчиняются только закону.
                    <br><br>
                    Вмешательство в деятельность судей по отправлению правосудия недопустимо и влечет ответственность по закону. Дела в судах рассматриваются коллегиально, а в предусмотренных законом случаях – единолично судьями.
                </p>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide card-constitution" onclick="window.location.href='{{ route('power.constitutional') }}'">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Конституционный Суд</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Осуществляет контроль конституционности нормативных актов</li>
                    <li>Состоит из 12 судей</li>
                    <li>6 судей назначаются Президентом</li>
                    <li>6 судей избираются Советом Республики</li>
                    <li>Дает заключения о конституционности законов</li>
                    <li>Разрешает споры о компетенции между госорганами</li>
                    <li>Председатель назначается Президентом с согласия Совета Республики</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-supreme" onclick="window.location.href='{{ route('power.supreme') }}'">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Верховный Суд</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Возглавляет систему судов общей юрисдикции</li>
                    <li>Является высшим судебным органом</li>
                    <li>Осуществляет надзор за судебной деятельностью</li>
                    <li>Рассматривает дела в качестве суда первой инстанции</li>
                    <li>Рассматривает дела в кассационном порядке</li>
                    <li>Дает разъяснения по вопросам судебной практики</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide card-courts">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Система судов</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Конституционный Суд</li>
                    <li>Верховный Суд</li>
                    <li>Областные (Минский городской) суды</li>
                    <li>Районные (городские) суды</li>
                    <li>Экономические суды областей</li>
                    <li>Военные суды</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-principles">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Принципы правосудия</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Независимость судей</li>
                    <li>Равенство всех перед законом</li>
                    <li>Состязательность и равноправие сторон</li>
                    <li>Гласность судебного разбирательства</li>
                    <li>Презумпция невиновности</li>
                    <li>Обеспечение права на защиту</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.card-constitution,
.card-supreme {
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.card-constitution:before,
.card-supreme:before {
    content: 'Нажмите для подробной информации';
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.8em;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 2;
    white-space: nowrap;
}

.card-constitution:hover:before,
.card-supreme:hover:before {
    opacity: 1;
}

.card-constitution .info-card-front,
.card-supreme .info-card-front {
    position: relative;
}

.card-constitution .info-card-front:after,
.card-supreme .info-card-front:after {
    content: '→';
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 24px;
    opacity: 0;
    transition: all 0.3s ease;
}

.card-constitution:hover .info-card-front:after,
.card-supreme:hover .info-card-front:after {
    opacity: 1;
    right: 15px;
}

.card-constitution:hover,
.card-supreme:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endsection
