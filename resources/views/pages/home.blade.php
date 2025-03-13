@extends('layouts.main')

@section('title', 'Главная')

@section('body-class', 'home-page')

@section('page-content')
<div class="power-structure">
    <video src="{{ asset('images/home.mp4') }}" autoplay muted loop playsinline style="position: absolute; top: 0; left: 0; width: 100%; object-fit: cover; filter: brightness(0.6);"></video>
    <div class="power-structure-container">
        <!-- Народ -->
        <div class="power-node people" id="people">
            <div class="node-content">
                <h2>Народ Республики Беларусь</h2>
                <p>Источник государственной власти</p>
                <a href="{{ route('power.people') }}" class="power-node-link" title="Подробнее">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Президент -->
        <div class="power-node president" id="president">
            <div class="node-content">
                <h2>Президент РБ</h2>
                <p>Глава государства</p>
                <a href="{{ route('power.president') }}" class="power-node-link" title="Подробнее">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </a>
            </div>
        </div>

        <!-- ВНС -->
        <div class="power-node assembly" id="assembly">
            <div class="node-content">
                <h2>Всебелорусское народное собрание</h2>
                <p>Высший представительный орган</p>
                <a href="{{ route('power.assembly') }}" class="power-node-link" title="Подробнее">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Исполнительная власть -->
        <div class="power-node executive" id="executive">
            <div class="node-content">
                <h2>Исполнительная власть</h2>
                <p>Совет Министров</p>
                <a href="{{ route('power.executive') }}" class="power-node-link" title="Подробнее">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Законодательная власть -->
        <div class="power-node legislative" id="legislative">
            <div class="node-content">
                <h2>Законодательная власть</h2>
                <p>Национальное собрание</p>
                <a href="{{ route('power.legislative') }}" class="power-node-link" title="Подробнее">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Судебная власть -->
        <div class="power-node judicial" id="judicial">
            <div class="node-content">
                <h2>Судебная власть</h2>
                <p>Конституционный Суд<br>Верховный Суд</p>
                <a href="{{ route('power.judicial') }}" class="power-node-link" title="Подробнее">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </a>
            </div>
        </div>

        <!-- SVG для стрелок -->
        <svg class="connections">
            <!-- Линии от народа -->
            <path class="connection" id="people-president" d=""></path>
            <path class="connection" id="people-assembly" d=""></path>

            <!-- Линии от президента -->
            <path class="connection" id="president-executive" d=""></path>
            <path class="connection" id="president-judicial" d=""></path>

            <!-- Линии от ВНС -->
            <path class="connection" id="assembly-legislative" d=""></path>
            <path class="connection" id="assembly-president" d=""></path>
        </svg>

    </div>
</div>

<style>
/* Добавляем стили для стрелок */
.connection {
    stroke: #fff; /* Белый цвет для стрелок */
    stroke-width: 3; /* Делаем линии толще */
    filter: drop-shadow(0 0 3px rgba(0,0,0,0.5)); /* Добавляем тень */
    transition: all 0.3s ease;
}

/* Стиль при наведении */
.connection:hover {
    stroke: #ffd700; /* Золотой цвет при наведении */
    stroke-width: 4; /* Увеличиваем толщину при наведении */
    filter: drop-shadow(0 0 5px rgba(255,215,0,0.7)); /* Добавляем светящийся эффект */
}

/* Добавляем затемнение для видео */
video {
    filter: brightness(0.4) !important; /* Делаем видео темнее */
}

/* Улучшаем видимость текста на узлах */
.power-node .node-content {
    background: rgba(0, 0, 0, 0.7); /* Полупрозрачный черный фон */
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
}

.power-node h2,
.power-node p {
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}
</style>
@endsection

@section('scripts')
@parent
<script src="{{ asset('js/interact.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/leader-line/leader-line.min.js"></script>

<script type="module">
    import PowerStructure from '{{ asset('js/modules/powerStructure.js') }}';
    new PowerStructure();
</script>
@endsection
