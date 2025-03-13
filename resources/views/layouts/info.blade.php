@extends('layouts.main')

@section('navbarTheme', 'navbar-light')

@section('page-content')
<div class="info-page">
    <div class="breadcrumbs-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">
                        <i class="ri-home-line"></i>
                        Главная
                    </a>
                </li>
                @yield('breadcrumbs')
            </ol>
        </nav>
    </div>

    <div class="info-container">
        <div class="info-content">
            @yield('info-content')
        </div>
        <div class="info-sidebar">
            @hasSection('qr-links')
                @yield('qr-links')
            @else
                <div class="qr-container">
                    <div class="qr-code" id="qr-code" alt="QR Code"></div>
                    <p class="qr-caption">Сканируйте для получения дополнительной информации</p>
                </div>
            @endif

            @hasSection('survey-link')
                <div class="survey-container">
                    <h4>Пройти опрос</h4>
                    <p>Пройдите опрос по данной теме, чтобы узнать уровень знаний</p>
                    <a href="@yield('survey-link')" class="survey-button">Пройти</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.breadcrumbs-container {
    padding: 1rem 0.5rem;
    background: #fff;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    z-index: 100;

    max-width: 1400px;
    margin: 0 auto;
    margin-bottom: 1rem;
    border-radius: 10px;
}

.breadcrumb {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item a {
    color: #2c3e50;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #3498db;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: '/';
    color: #95a5a6;
    margin-right: 0.5rem;
}

.breadcrumb-item.active {
    color: #7f8c8d;
}

.ri-home-line {
    font-size: 1.2rem;
}

.back-button-container {
    padding: 1rem 2rem;
    position: sticky;
    top: 70px;
    z-index: 100;
}

.back-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.3s ease;
    color: #2c3e50;
}

.back-button:hover {
    transform: translateX(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.back-button i {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.back-button:hover i {
    transform: translateX(-3px);
}

.back-button span {
    font-weight: 500;
}

@media (max-width: 768px) {
    .breadcrumbs-container {
        padding: 0.5rem 1rem;
    }

    .breadcrumb {
        font-size: 0.9rem;
    }

    .back-button-container {
        padding: 0.5rem 1rem;
    }

    .back-button {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
}

.qr-links {
    display: flex;
    justify-content: center;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.qr-link-item {
    text-align: center;
}

.qr-title {
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.qr-link-item {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.qr-title {
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #2c3e50;
    font-size: 0.9em;
    text-align: center;
}

.qr-code {
    display: flex;
    justify-content: center;
    margin: 0 auto;
}

.qr-container {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.qr-caption {
    text-align: center;
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.9em;
}
</style>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/qrcode.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Для одиночного QR-кода
            const singleQrContainer = document.getElementById('qr-code');
            if (singleQrContainer) {
                singleQrContainer.innerHTML = '';
                new QRCode(singleQrContainer, {
                    text: "@yield('qr-link', request()->url())",
                    width: 180,
                    height: 180,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }

            // Для множественных QR-кодов
            const qrContainers = document.querySelectorAll('.qr-code[data-url]');
            qrContainers.forEach(container => {
                container.innerHTML = '';
                new QRCode(container, {
                    text: container.dataset.url,
                    width: 180,
                    height: 180,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });

        } catch (error) {
            console.error('Error creating QR code:', error);
        }
    });
</script>
@endpush
