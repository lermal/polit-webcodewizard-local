@extends('layouts.info')

@section('title', 'Законодательная власть')

@section('qr-links')
<div class="qr-links">
    <div class="qr-link-item">
        <div class="qr-title">Палата представителей</div>
        <div class="qr-code" data-url="http://www.house.gov.by"></div>
        <p class="qr-caption">Сканируйте для получения дополнительной информации</p>
    </div>
    <div class="qr-link-item">
        <div class="qr-title">Совет Республики</div>
        <div class="qr-code" data-url="http://www.sovrep.gov.by"></div>
        <p class="qr-caption">Сканируйте для получения дополнительной информации</p>
    </div>
</div>
@endsection

@section('info-content')
<h1>Законодательная власть</h1>

<p>
    Парламент – Национальное собрание Республики Беларусь является представительным и законодательным органом страны.
</p>

<div class="info-cards">
    <!-- Карточка на всю ширину -->
    <div class="info-card card-full card-parliament">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Национальное собрание</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Парламент состоит из двух палат – Палаты представителей и Совета Республики. Срок полномочий Парламента – четыре года. Палаты заседают раздельно, за исключением установленных случаев проведения совместных заседаний.
                    <br><br>
                    Национальное собрание рассматривает проекты законов об основных направлениях внутренней и внешней политики, о военной доктрине, ратификации и денонсации международных договоров, об основном содержании и принципах осуществления прав, свобод и обязанностей граждан.
                    <br><br>
                    Законопроекты, следствием принятия которых может быть сокращение государственных средств, создание или увеличение расходов, могут вноситься в Палату представителей лишь с согласия Президента либо по его поручению – Правительства.
                </p>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide card-representatives" onclick="window.location.href='{{ route('power.representatives') }}'">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Палата представителей</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Состоит из 110 депутатов</li>
                    <li>Избирается прямым всеобщим голосованием</li>
                    <li>Рассматривает проекты законов</li>
                    <li>Назначает выборы Президента</li>
                    <li>Дает согласие на назначение Премьер-министра</li>
                    <li>Заслушивает доклад Премьер-министра о программе деятельности Правительства</li>
                    <li>Рассматривает вопрос о доверии Правительству</li>
                    <li>Принимает отставку Президента</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-council" onclick="window.location.href='{{ route('power.council') }}'">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Совет Республики</h2>
            </div>
            <div class="info-card-back">
                <ul class="info-card-list">
                    <li>Является палатой территориального представительства</li>
                    <li>От каждой области и г. Минска избирается по 8 членов</li>
                    <li>8 членов назначаются Президентом</li>
                    <li>Одобряет или отклоняет принятые законы</li>
                    <li>Отменяет решения местных Советов депутатов</li>
                    <li>Избирает 6 судей Конституционного Суда</li>
                    <li>Дает согласие на назначение Председателя Конституционного Суда</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Две карточки по 50% -->
    <div class="info-card card-wide card-laws">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Законотворческий процесс</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Право законодательной инициативы принадлежит:
                    <br>• Президенту
                    <br>• Депутатам Палаты представителей
                    <br>• Совету Республики
                    <br>• Правительству
                    <br>• Гражданам (не менее 50 тысяч человек)
                    <br><br>
                    Законопроект проходит два чтения в Палате представителей, затем рассматривается Советом Республики и подписывается Президентом.
                </p>
            </div>
        </div>
    </div>

    <div class="info-card card-wide card-requirements">
        <div class="info-card-inner">
            <div class="info-card-front">
                <h2 class="info-card-title">Требования к депутатам</h2>
            </div>
            <div class="info-card-back">
                <p class="info-card-text">
                    Депутатом Палаты представителей может быть гражданин Республики Беларусь, достигший 21 года.
                    <br><br>
                    Членом Совета Республики может быть гражданин Республики Беларусь, достигший 30 лет и проживший на территории соответствующей области не менее пяти лет.
                    <br><br>
                    Депутаты и члены Совета Республики не могут быть одновременно членами Правительства.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('breadcrumbs')
<li class="breadcrumb-item active">Законодательная власть</li>
@endsection

<style>

.card-representatives,
.card-council {
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.card-representatives:before,
.card-council:before {
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

.card-representatives:hover:before,
.card-council:hover:before {
    opacity: 1;
}

.card-representatives .info-card-front,
.card-council .info-card-front {
    position: relative;
}

.card-representatives .info-card-front:after,
.card-council .info-card-front:after {
    content: '→';
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 24px;
    opacity: 0;
    transition: all 0.3s ease;
}

.card-representatives:hover .info-card-front:after,
.card-council:hover .info-card-front:after {
    opacity: 1;
    right: 15px;
}

.card-representatives:hover,
.card-council:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}


/* Убираем stretched-link, так как теперь кликабельна вся карточка */
.stretched-link {
    pointer-events: none;
    text-decoration: none;
    color: inherit;
}
</style>
