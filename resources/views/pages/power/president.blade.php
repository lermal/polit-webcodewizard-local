@extends('layouts.info')

@section('title', 'Президент Республики Беларусь')
@section('qr-link', 'https://president.gov.by/ru')

@section('info-content')
<h1>Президент Республики Беларусь</h1>

<p>
    Президент Республики Беларусь является Главой государства, гарантом Конституции Республики Беларусь, прав и свобод человека и гражданина.
</p>

<div class="info-cards">
    <!-- Карточка на всю ширину -->
    <div class="info-card card-full card-president">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Александр Григорьевич Лукашенко</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Президент обладает неприкосновенностью, его честь и достоинство охраняются законом.
                    <br><br>
                    Президент, прекративший исполнение полномочий в связи с истечением срока его пребывания в должности либо досрочно в случае его отставки или стойкой неспособности по состоянию здоровья осуществлять обязанности Президента также обладает неприкосновенностью.
                </p>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide card-constitution">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Президент в конституции</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    По конституции Республики Беларусь, президент - это высший орган исполнительной власти и глава государства.
                    Он избирается населением на пятилетний срок путем всеобщих выборов.
                    Президент Республики Беларусь обладает значительными полномочиями в сфере управления страной.
                </p>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-powers">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Полномочия президента</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>формирует Правительство</li>
                    <li>представляет Беларусь в отношениях с другими государствами</li>
                    <li>является Главнокомандующим Вооруженными Силами</li>
                    <li>принимает меры по охране суверенитета</li>
                    <li>назначает на высшие государственные должности</li>
                    <li>издает декреты, указы и распоряжения</li>
                    <li>обращается с посланиями к народу</li>
                    <li>награждает государственными наградами</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide card-elections">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Выборы президента</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Президент избирается сроком на пять лет непосредственно народом Республики Беларусь на основе всеобщего, свободного, равного и прямого избирательного права при тайном голосовании. Ближайшие выборы президента назначены на 25 февраля 2024 года.
                </p>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-requirements">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Требования к кандидату</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>гражданин Республики Беларусь по рождению</li>
                    <li>не моложе 40 лет</li>
                    <li>обладающий избирательным правом</li>
                    <li>постоянно проживающий в Республике Беларусь не менее 20 лет</li>
                    <li>не имеющий и не имевший иностранного гражданства</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
