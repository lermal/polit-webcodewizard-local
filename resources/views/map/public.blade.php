@extends('layouts.info')

@section('title', 'Интерактивная карта')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
@endpush

@section('breadcrumbs')
<li class="breadcrumb-item active">Интерактивная карта</li>
@endsection

@section('info-content')
<div class="info-cards">
    <!-- Карта на всю ширину -->
    <div class="info-card card-full">
        <div class="map-wrapper">
            <div id="map"></div>
        </div>
    </div>

    <!-- Панель маршрутов -->
    <div class="info-card card-full">
        <div class="routes-panel">
            <h2>Доступные маршруты</h2>
            <div class="routes-grid">
                @foreach($routes as $route)
                <div class="route-item">
                    <div class="route-header">
                        <h3>{{ $route->name }}</h3>
                        <div class="route-meta">
                            <span class="route-stat">
                                <i class="ri-map-pin-line"></i> {{ $route->markers->count() }} точек
                            </span>
                            <span class="route-stat">
                                <i class="ri-user-line"></i> {{ $route->creator->name }}
                            </span>
                        </div>
                    </div>
                    <p class="route-description">{{ $route->description }}</p>
                    <div class="route-actions">
                        @if($route->voting_enabled)
                        <div class="route-votes">
                            <span class="votes-count">{{ $route->votes_count }}</span>
                            @auth
                            <button class="custom-button btn-vote" data-route-id="{{ $route->id }}">
                                <i class="ri-thumb-up-line"></i>
                                <span class="button-overlay"></span>
                            </button>
                            @endauth
                        </div>
                        @endif
                        @can('start_route_voting')
                        @if(!$route->voting_enabled)
                        <button class="custom-button btn-start-voting" data-route-id="{{ $route->id }}">
                            Начать голосование
                            <span class="button-overlay"></span>
                        </button>
                        @endif
                        @endcan
                        <button class="custom-button view-route" data-route-id="{{ $route->id }}">
                            Просмотреть маршрут
                            <span class="button-overlay"></span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.map-wrapper {
    height: 600px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
}

/* Стили для тайлов */
/*  */

    </style>
@endsection

@section('qr-links')
<div class="qr-links">
    <div class="qr-link-item">
        <div class="qr-title">Скачать карту</div>
        <div class="qr-code" data-url="{{ url()->current() }}"></div>
    </div>
</div>
@endsection

@push('styles')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

// Инициализация карты
let map, markersGroup, belarusLayer, routeLine;
const customIcon = L.divIcon({
    html: '<i class="ri-map-pin-2-fill" style="font-size: 24px; color: #2c3e50;"></i>',
    className: 'custom-div-icon',
    iconSize: [30, 30],
    iconAnchor: [15, 15]
});

function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    try {
        // Инициализация карты
        map = L.map('map', {
            center: [53.9, 27.5667],
            zoom: 6,
            minZoom: 6,
            maxZoom: 18,
        });

        // Добавляем подложку карты
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        markersGroup = L.featureGroup().addTo(map);

        // Загружаем границы Беларуси и маркеры
        // loadBelarusBorders();
        // loadExistingMarkers();

    } catch (error) {
        console.error('Error initializing map:', error);
    }
}

// }

// function loadBelarusBorders() {
//     fetch('/js/belarus.geojson')
//         .then(response => response.json())
//         .then(data => {
//             // Создаем отдельный pane для границ
//             if (!map.getPane('borders')) {
//                 map.createPane('borders');
//                 map.getPane('borders').style.zIndex = 650;
//             }

//             belarusLayer = L.geoJSON(data, {
//                 pane: 'borders',
//                 style: {
//                     color: '#2c3e50',
//                     weight: 3,
//                     opacity: 0.8,
//                     fillColor: '#2c3e50',
//                     fillOpacity: 0.1,
//                     dashArray: '5, 10'
//                 },
//                 interactive: false,
//                 renderer: L.svg()
//             }).addTo(map);

//             // Устанавливаем границы карты по границам Беларуси
//             map.fitBounds(belarusLayer.getBounds(), {
//                 padding: [20, 20]
//             });

//             // Ограничиваем перемещение карты границами Беларуси
//             map.setMaxBounds(belarusLayer.getBounds().pad(0.1));
//         });
// }

// function loadExistingMarkers() {
//     fetch('/map/markers', {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         }
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error(`HTTP error! status: ${response.status}`);
//         }
//         return response.json();
//     })
//     .then(data => {
//         if (!data.markers) return;

//         data.markers.forEach(marker => {
//             const markerObj = L.marker([marker.latitude, marker.longitude], {
//                 icon: customIcon
//             }).addTo(markersGroup);

//             markerObj.bindTooltip(`
//                 <div>
//                     <strong>${marker.title}</strong><br>
//                     ${marker.description}
//                 </div>
//             `, {
//                 permanent: false,
//                 direction: 'top',
//                 className: 'marker-tooltip'
//             });
//         });
//     })
//     .catch(error => {
//         console.error('Error loading markers:', error);
//         Swal.fire({
//             icon: 'error',
//             title: 'Ошибка загрузки маркеров',
//             text: 'Не удалось загрузить маркеры на карту'
//         });
//     });
// }

// function initRouteControls() {
//     // Обработчик кнопки показа списка маршрутов
//     $('#toggleRoutes').on('click', function() {
//         $('.routes-list').toggleClass('hidden');
//     });

//     // Закрытие списка маршрутов
//     $('.close-routes').on('click', function() {
//         $('.routes-list').addClass('hidden');
//     });

//     // Просмотр маршрута
//     $('.view-route').on('click', function() {
//         const routeId = $(this).data('route-id');
//         viewRoute(routeId);
//     });

//     // Голосование за маршрут
//     $('.btn-vote').on('click', function() {
//         const routeId = $(this).data('route-id');
//         const $btn = $(this);
//         const $votesCount = $btn.closest('.route-votes').find('.votes-count');

//         fetch(`/routes/${routeId}/vote`, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//             },
//             body: JSON.stringify({ vote: true })
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 $votesCount.text(data.votes_count);
//                 $btn.toggleClass('voted');
//             }
//         });
//     });

//     // Начать голосование
//     $('.btn-start-voting').on('click', function() {
//         const routeId = $(this).data('route-id');
//         const $btn = $(this);

//         fetch(`/routes/${routeId}/start-voting`, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 $btn.replaceWith('<div class="route-votes"><span class="votes-count">0</span></div>');
//             }
//         });
//     });
// }

// function viewRoute(routeId) {
//     fetch(`/map/routes/${routeId}/view`)
//         .then(response => response.json())
//         .then(data => {
//             // Очищаем предыдущий маршрут
//             markersGroup.clearLayers();
//             if (routeLine) {
//                 map.removeLayer(routeLine);
//             }

//             // Добавляем маркеры
//             const markers = data.markers.map(marker => {
//                 return L.marker([marker.latitude, marker.longitude], {
//                     icon: customIcon
//                 }).addTo(markersGroup)
//                 .bindTooltip(`
//                     <div>
//                         <strong>${marker.title}</strong><br>
//                         ${marker.description}
//                     </div>
//                 `);
//             });

//             // Строим маршрут между маркерами
//             const coordinates = data.markers
//                 .map(marker => `${marker.longitude},${marker.latitude}`)
//                 .join(';');

//             fetch(`https://router.project-osrm.org/route/v1/driving/${coordinates}?geometries=geojson`)
//                 .then(response => response.json())
//                 .then(routeData => {
//                     if (routeData.code === 'Ok') {
//                         routeLine = L.geoJSON(routeData.routes[0].geometry, {
//                             style: {
//                                 color: '#2c3e50',
//                                 weight: 4,
//                                 opacity: 0.8
//                             }
//                         }).addTo(map);

//                         // Центрируем карту на маршруте
//                         map.fitBounds(routeLine.getBounds(), {
//                             padding: [50, 50]
//                         });
//                     }
//                 });
//         });
// }
// </script>
@endpush
