<ul class="side-nav">
    <li class="side-nav-item">
        <a href="{{ route('user.dashboard') }}" class="side-nav-link">
            <i class="ri-dashboard-line"></i>
            <span> Главная </span>
        </a>
    </li>

    <li class="side-nav-item">
        <a href="{{ route('user.settings') }}" class="side-nav-link">
            <i class="ri-settings-2-line"></i>
            <span> Настройки </span>
        </a>
    </li>

    <li class="side-nav-item">
        <a href="{{ route('user.tests.index') }}" class="side-nav-link">
            <i class="ri-file-list-3-line"></i>
            <span> Тесты </span>
        </a>
    </li>

    @if(auth()->user()->hasRole('teacher') || auth()->user()->isAdmin())
    <li class="side-nav-title">Учителя</li>
    <li class="side-nav-item">
        <a href="{{ route('teacher.map') }}" class="side-nav-link">
            <i class="ri-settings-4-line"></i>
            <span> Управление маршрутами </span>
        </a>
    </li>
    @endif

    @if(auth()->user()->isAdmin())
    <li class="side-nav-title">Администрирование</li>

    <li class="side-nav-item">
        <a href="{{ route('admin.tests.index') }}" class="side-nav-link">
            <i class="ri-file-list-3-line"></i>
            <span> Управление тестами </span>
        </a>
    </li>

    <li class="side-nav-item">
        <a href="{{ route('admin.users') }}" class="side-nav-link">
            <i class="ri-group-line"></i>
            <span> Пользователи </span>
        </a>
    </li>

    <li class="side-nav-item">
        <a href="{{ route('admin.settings') }}" class="side-nav-link">
            <i class="ri-settings-4-line"></i>
            <span> Настройки сайта </span>
        </a>
    </li>

    <li class="side-nav-item">
        <a href="{{ route('admin.map') }}" class="side-nav-link">
            <i class="ri-map-pin-line"></i>
            <span> Управление картой </span>
        </a>
    </li>
    @endif
</ul>
