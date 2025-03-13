@extends('template.index')

@section('title', 'Управление маршрутами')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-header bg-transparent">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Управление маршрутами</h6>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Левая колонка -->
                            <div class="col-md-4">
                                <!-- Список маршрутов -->
                                <div id="routesList" class="card border shadow-none radius-10">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h5 class="mb-0">Мои маршруты</h5>
                                            </div>
                                        </div>
                                        <div class="routes-list mt-3">
                                            @if($routes->count() > 0)
                                            @foreach($routes as $route)
                                            <div class="route-item d-flex align-items-center border-top py-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $route->name }}</h6>
                                                    <span class="badge bg-light text-dark">{{ $route->markers->count() }} точек</span>
                                                    <small class="text-muted d-block">Автор: {{ $route->creator->name }}</small>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-light view-route" data-route-id="{{ $route->id }}" title="Просмотреть">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    @if(auth()->user()->isAdmin() || auth()->id() === $route->created_by)
                                                    <button class="btn btn-sm btn-light-danger delete-route" data-route-id="{{ $route->id }}" title="Удалить">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                            @else
                                            <div class="text-center text-muted py-3">
                                                <p>Пока нет созданных маршрутов</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Меню создания маршрута -->
                                <div class="route-control card border shadow-none radius-10" id="routeControl">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Создание маршрута</h6>
                                            <button type="button" class="btn-close" id="closeRouteControl"></button>
                                        </div>
                                        <div class="mt-3">
                                            <input type="text" class="form-control" id="routeName" placeholder="Название маршрута">
                                            <textarea class="form-control mt-2" id="routeDescription" placeholder="Описание маршрута" rows="3"></textarea>
                                        </div>
                                        <div class="selected-markers" id="selectedMarkers">
                                            <!-- Здесь будут выбранные маркеры -->
                                        </div>
                                        <button class="btn btn-primary create-route-btn" id="createRoute" disabled>
                                            Создать маршрут
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Правая колонка - карта -->
                            <div class="col-md-8" id="mapColumn">
                                <div class="card border shadow-none radius-10">
                                    <div class="card-body p-0">
                                        <div id="map" style="height: 600px; width: 100%; border-radius: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно создания маршрута -->
<div class="modal fade" id="createRouteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Создание маршрута</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createRouteForm">
                    <div class="mb-3">
                        <label class="form-label">Название маршрута</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Точки маршрута</label>
                        <div class="markers-list border radius-10 p-3">
                            <!-- Здесь будет список доступных маркеров -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="saveRoute">Сохранить</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="{{ asset('css/map.css') }}">
<style>
    .route-control {
        display: none !important;
    }

    .route-control.active {
        display: block !important;
    }

    .selected-markers {
        margin-top: 10px;
    }

    .selected-marker-item {
        display: flex;
        align-items: center;
        padding: 8px;
        background: #f8f9fa;
        margin-bottom: 5px;
        border-radius: 4px;
        cursor: move;
    }

    .remove-marker {
        margin-left: auto;
        color: #dc3545;
        cursor: pointer;
    }

    .create-route-btn {
        width: 100%;
        margin-top: 10px;
    }

    .custom-number-icon {
        background: none;
        border: none;
    }

    .marker-number {
        transition: all 0.3s ease;
    }

    .marker-number:hover {
        transform: scale(1.1);
        box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
    }

    .animated-line {
        stroke-dasharray: 8, 16;
        animation: dash 1s linear infinite;
    }

    @keyframes dash {
        to {
            stroke-dashoffset: -24;
        }
    }

    .leaflet-tooltip {
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #2c3e50;
        border-radius: 4px;
        padding: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        white-space: nowrap;
        max-width: 300px;
    }

    .leaflet-tooltip-top:before {
        border-top-color: #2c3e50;
    }

    .marker-tooltip strong {
        color: #2c3e50;
        display: block;
        margin-bottom: 4px;
    }

    #routesList.hidden {
        display: none;
    }

    .col-md-8.expanded {
        width: 100%;
    }

    #routeControl {
        width: 100%;
        box-shadow: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://unpkg.com/leaflet-polylinedecorator/dist/leaflet.polylineDecorator.js"></script>
<script>
    // Объявляем глобальные переменные и функции
    let map, markersGroup, belarusLayer, tempMarker, customIcon, routeLine;
    let selectedMarkers = [];
    let isRouteCreationMode = false;
    let selectedMarkersList;
    let routeControl, createRouteBtn, routeName, routeDescription;

    // Объявляем объект для кэширования
    const routeCache = {
        get: function(coordinates) {
            try {
                const cached = localStorage.getItem(`route_${coordinates}`);
                if (cached) {
                    const { data, timestamp } = JSON.parse(cached);
                    // Проверяем срок годности кэша (24 часа)
                    if (Date.now() - timestamp < 24 * 60 * 60 * 1000) {
                        return data;
                    }
                    localStorage.removeItem(`route_${coordinates}`);
                }
            } catch (error) {
                console.error('Error reading from cache:', error);
            }
            return null;
        },

        set: function(coordinates, data) {
            try {
                localStorage.setItem(`route_${coordinates}`, JSON.stringify({
                    data: data,
                    timestamp: Date.now()
                }));
            } catch (error) {
                console.error('Error writing to cache:', error);
                this.clearOld();
            }
        },

        clearOld: function() {
            try {
                const keys = Object.keys(localStorage);
                const routeKeys = keys.filter(key => key.startsWith('route_'));

                const sortedKeys = routeKeys.sort((a, b) => {
                    const timeA = JSON.parse(localStorage.getItem(a)).timestamp;
                    const timeB = JSON.parse(localStorage.getItem(b)).timestamp;
                    return timeB - timeA;
                });

                // Оставляем только последние 50 маршрутов
                sortedKeys.slice(50).forEach(key => localStorage.removeItem(key));
            } catch (error) {
                console.error('Error clearing cache:', error);
                const keys = Object.keys(localStorage);
                keys.filter(key => key.startsWith('route_')).forEach(key => localStorage.removeItem(key));
            }
        }
    };

    // Используем jQuery для инициализации
    $(document).ready(function() {
        // Инициализируем все DOM элементы
        selectedMarkersList = document.getElementById('selectedMarkers');
        routeControl = $('#routeControl');
        createRouteBtn = $('#createRoute');
        routeName = $('#routeName');
        routeDescription = $('#routeDescription');

        initMap();
        initRouteControls();
    });

    function initMap() {
        const mapElement = document.getElementById('map');
        if (!mapElement) {
            console.error('Map element not found');
            return;
        }

        try {
            // Создаем кастомную иконку маркера
            customIcon = L.divIcon({
                html: '<i class="ri-map-pin-2-fill" style="font-size: 24px; color: #2c3e50;"></i>',
                className: 'custom-div-icon',
                iconSize: [30, 30],
                iconAnchor: [15, 15],
                popupAnchor: [0, -30]
            });

            // Инициализация карты
            map = L.map('map', {
                center: [53.9, 27.5667],
                zoom: 6,
                minZoom: 6,
                maxZoom: 15
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            markersGroup = L.featureGroup().addTo(map);

            // Загружаем границы Беларуси и маркеры
            loadBelarusBorders();
            loadExistingMarkers();

        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    function initRouteControls() {
        if (routeControl.length && selectedMarkersList) {
            // Инициализация Sortable
            new Sortable(selectedMarkersList, {
                animation: 150,
                onEnd: function() {
                    updateSelectedMarkersOrder();
                }
            });

            // Обработчики для панели управления маршрутом
            $('#closeRouteControl').on('click', function() {
                selectedMarkers = [];
                updateSelectedMarkersList();
                if (routeLine) {
                    map.removeLayer(routeLine);
                    routeLine = null;
                }
                toggleRouteCreationMode();

                // Возвращаем исходный заголовок
                $('#routeControlTitle').text('Создание маршрута');

                // Возвращаем исходные иконки маркерам
                markersGroup.eachLayer((layer) => {
                    layer.setIcon(customIcon);
                });
            });

            // Обработчики ввода для валидации формы
            routeName.add(routeDescription).on('input', validateRouteForm);

            // Обработчик создания маршрута
            createRouteBtn.on('click', function() {
                const routeData = {
                    name: routeName.val(),
                    description: routeDescription.val(),
                    markers: selectedMarkers.map(m => m.id)
                };
                saveRoute(routeData);
            });

            // Добавляем обработчик для кнопок просмотра маршрута
            $(document).on('click', '.view-route', function() {
                const routeId = $(this).data('route-id');
                viewRoute(routeId);
            });
        }
    }

    function loadBelarusBorders() {
        fetch('/js/belarus.geojson')
            .then(response => response.json())
            .then(data => {
                belarusLayer = L.geoJSON(data, {
                    style: {
                        color: '#2c3e50',
                        weight: 3,
                        opacity: 0.8,
                        fillColor: 'transparent',
                        fillOpacity: 0.1,
                        dashArray: '5, 10'
                    }
                }).addTo(map);

                map.fitBounds(belarusLayer.getBounds(), {
                    padding: [20, 20]
                });
            });
    }

    function loadExistingMarkers() {
        fetch('/api/markers')
            .then(response => response.json())
            .then(data => {
                if (!data.markers) return;

                data.markers.forEach(marker => {
                    // Создаем Leaflet маркер
                    const markerObj = L.marker([marker.latitude, marker.longitude], {
                            icon: customIcon
                        })
                        .addTo(markersGroup);

                    // Добавляем тултип
                    markerObj.bindTooltip(`
                    <div>
                        <strong>${marker.title}</strong><br>
                        ${marker.description}
                    </div>
                `, {
                        permanent: false,
                        direction: 'top',
                        className: 'marker-tooltip marker-tooltip-name'
                    });

                    markerObj.on('click', function() {
                        if (!routeControl.hasClass('active')) {
                            routeControl.addClass('active');
                            isRouteCreationMode = true;
                        }

                        // Создаем объект маркера со всеми необходимыми свойствами
                        const markerData = {
                            id: marker.id,
                            title: marker.title,
                            description: marker.description,
                            latitude: marker.latitude,
                            longitude: marker.longitude,
                            leafletMarker: markerObj
                        };

                        // Проверяем, не добавлен ли уже этот маркер
                        if (!selectedMarkers.find(m => m.id === marker.id)) {
                            selectedMarkers.push(markerData);
                            updateSelectedMarkersList();

                            // Вызываем переключение режима при добавлении первого маркера
                            if (selectedMarkers.length === 1) {
                                toggleRouteCreationMode();
                            }
                        }
                    });
                });
            })
            .catch(error => {
                console.error('Error loading markers:', error);
            });
    }

    function validateRouteForm() {
        if (createRouteBtn && createRouteBtn.length) {
            createRouteBtn.prop('disabled', !routeName.val() || !routeDescription.val() || selectedMarkers.length < 2);
        }
    }

    function removeMarker(index) {
        if (index === -1) return; // Защита от некорректного индекса

        const marker = selectedMarkers[index];
        // Возвращаем оригинальную иконку маркеру
        if (marker.leafletMarker) {
            marker.leafletMarker.setIcon(customIcon);
            marker.leafletMarker.unbindTooltip();
            marker.leafletMarker.bindTooltip(`
        <div>
            <strong>${marker.title}</strong><br>
            ${marker.description}
        </div>
    `, {
                permanent: false,
                direction: 'top',
                className: 'marker-tooltip marker-tooltip-name'
            });
        }

        selectedMarkers.splice(index, 1);
        updateSelectedMarkersList();
        updateRouteLine(); // Это обновит и маршрут, и информацию о нём
        validateRouteForm(); // Проверяем валидность формы после удаления маркера
    }

    function updateSelectedMarkersOrder() {
        // Сначала удаляем старый маршрут
        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }

        const newOrder = Array.from(selectedMarkersList.children).map(item => {
            const markerId = parseInt(item.dataset.markerId);
            return selectedMarkers.find(m => m.id === markerId);
        });
        selectedMarkers = newOrder;
        updateSelectedMarkersList();
    }

    // Добавляем новую функцию для создания нумерованной иконки
    function createNumberedIcon(number) {
        return L.divIcon({
            html: `
            <div style="
                background-color: #2c3e50;
                color: white;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 14px;
                border: 2px solid white;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            ">
                ${number}
            </div>
        `,
            className: 'step-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
    }

    // Добавляем функцию обновления иконок маркеров
    function updateMarkerIcons() {
        // Сначала возвращаем всем маркерам исходную иконку
        markersGroup.eachLayer((layer) => {
            layer.setIcon(customIcon);

            // Возвращаем исходный тултип
            if (layer.getTooltip()) {
                const markerData = selectedMarkers.find(m =>
                    m.leafletMarker === layer
                );
                if (markerData) {
                    layer.unbindTooltip();
                    layer.bindTooltip(`
                        <div>
                            <strong>${markerData.title}</strong><br>
                            ${markerData.description}
                        </div>
                    `, {
                        permanent: false,
                        direction: 'top'
                    });
                }
            }
        });

        // Затем обновляем иконки выбранных маркеров
        selectedMarkers.forEach((marker, index) => {
            if (marker.leafletMarker) {
                marker.leafletMarker.setIcon(createNumberedIcon(index + 1));

                // Обновляем тултип
                marker.leafletMarker.unbindTooltip();
                marker.leafletMarker.bindTooltip(`
                    <div>
                        <strong>Точка ${index + 1}: ${marker.title}</strong><br>
                        ${marker.description}
                    </div>
                `, {
                    permanent: false,
                    direction: 'top'
                });
            }
        });
    }

    // Обновляем функцию обновления списка маршрутов
    function updateSelectedMarkersList() {
        if (!selectedMarkersList) {
            selectedMarkersList = document.getElementById('selectedMarkers');
        }

        if (selectedMarkersList) {
            selectedMarkersList.innerHTML = selectedMarkers.map((marker, index) => `
            <div class="selected-marker-item" data-marker-id="${marker.id}">
                <span class="me-2">${index + 1}.</span>
                <span>${marker.title}</span>
                <i class="ri-delete-bin-line remove-marker" data-marker-id="${marker.id}"></i>
            </div>
        `).join('');
            validateRouteForm();
            updateRouteLine();
            updateMarkerIcons(); // Добавляем вызов функции обновления иконок
        }
    }

    async function updateRouteLine() {
        if (routeLine) {
            map.removeLayer(routeLine);
        }

        if (selectedMarkers.length < 2) {
            let routeInfoContainer = document.getElementById('routeInfo');
            if (routeInfoContainer) {
                routeInfoContainer.innerHTML = '';
            }
            return;
        }

        try {
            let routeMarkers = [...selectedMarkers];
            if (selectedMarkers.length > 2) {
                routeMarkers.push(selectedMarkers[0]);
            }

            const coordinates = routeMarkers
                .map(marker => `${marker.longitude},${marker.latitude}`)
                .join(';');

            // Проверяем кэш перед запросом
            let routeData = routeCache.get(coordinates);

            if (!routeData) {
                console.log('Fetching new route data');
                const response = await fetch(
                    `https://router.project-osrm.org/route/v1/driving/${coordinates}?geometries=geojson`,
                    {
                        method: 'GET',
                        mode: 'cors',
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                routeData = await response.json();

                // Сохраняем в кэш только успешные ответы
                if (routeData.code === 'Ok' && routeData.routes[0]) {
                    routeCache.set(coordinates, routeData);
                }
            } else {
                console.log('Using cached route data');
            }

            if (routeData.code === 'Ok' && routeData.routes[0]) {
                routeLine = L.geoJSON(routeData.routes[0].geometry, {
                    style: {
                        color: '#2c3e50',
                        weight: 4,
                        opacity: 0.8,
                        lineJoin: 'round',
                        className: 'animated-line'
                    }
                }).addTo(map);

                // Добавляем декоратор для стрелок направления
                const decorator = L.polylineDecorator(routeLine, {
                    patterns: [{
                            offset: 25,
                            repeat: 50,
                            symbol: L.Symbol.arrowHead({
                                pixelSize: 15,
                                polygon: false,
                                pathOptions: {
                                    color: '#2c3e50',
                                    weight: 3,
                                    opacity: 0.8
                                }
                            })
                        },
                        {
                            offset: 0,
                            repeat: 250,
                            symbol: L.Symbol.marker({
                                rotate: true,
                                markerOptions: {
                                    icon: L.divIcon({
                                        html: '<i class="ri-arrow-right-line" style="color: #2c3e50; font-size: 16px;"></i>',
                                        className: 'route-direction-marker',
                                        iconSize: [20, 20]
                                    })
                                }
                            })
                        }
                    ]
                }).addTo(map);

                // Подстраиваем карту под маршрут
                map.fitBounds(routeLine.getBounds(), {
                    padding: [50, 50]
                });

                // Обновляем информацию о маршруте
                let routeInfoContainer = document.getElementById('routeInfo');
                if (!routeInfoContainer) {
                    routeInfoContainer = document.createElement('div');
                    routeInfoContainer.id = 'routeInfo';
                    document.getElementById('selectedMarkers').after(routeInfoContainer);
                }

                const distance = (routeData.routes[0].distance / 1000).toFixed(1);
                const duration = Math.round(routeData.routes[0].duration / 60);
                const returnInfo = selectedMarkers.length > 2 ?
                    '<i class="ri-refresh-line me-2"></i> Круговой маршрут (с возвратом в начальную точку)<br>' : '';

                routeInfoContainer.innerHTML = `
                <div class="route-info mt-3">
                    <div class="alert alert-info">
                        ${returnInfo}
                        <i class="ri-route-line me-2"></i> Общее расстояние: ${distance} км<br>
                        <i class="ri-time-line me-2"></i> Общее время в пути: ${duration} мин<br>
                        <i class="ri-map-pin-line me-2"></i> Количество точек: ${selectedMarkers.length}
                    </div>
                    <div class="route-steps mt-2">
                        <h6>Маршрут:</h6>
                        ${selectedMarkers.map((marker, index) => `
                            <div class="route-step">
                                <span class="badge bg-primary me-2">${index + 1}</span>
                                ${marker.title}
                                ${index < selectedMarkers.length - 1 ? '<i class="ri-arrow-right-line mx-2"></i>' : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            } else {
                throw new Error('Invalid OSRM response');
            }

        } catch (error) {
            console.error('Error generating route:', error);
            Swal.fire({
                icon: 'error',
                title: 'Ошибка построения маршрута',
                text: 'Не удалось построить маршрут между выбранными точками. Пожалуйста, попробуйте выбрать другие точки.'
            });
        }
    }

    function toggleRouteCreationMode() {
        const routesList = $('#routesList');
        const routeControl = $('#routeControl');

        if (selectedMarkers.length > 0) {
            routesList.addClass('hidden');
            routeControl.addClass('active');
            isRouteCreationMode = true;
        } else {
            routesList.removeClass('hidden');
            routeControl.removeClass('active');
            isRouteCreationMode = false;
        }
    }

    function saveRoute(routeData) {
        fetch('/api/routes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(routeData)
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire('Успешно', 'Маршрут создан', 'success').then(() => {
                // Перезагружаем страницу для обновления списка маршрутов
                window.location.reload();
            });

            // Очищаем состояние
            routeControl.removeClass('active');
            isRouteCreationMode = false;
            selectedMarkers = [];
            updateSelectedMarkersList();
            if (routeLine) {
                map.removeLayer(routeLine);
                routeLine = null;
            }
            routeName.val('');
            routeDescription.val('');
        })
        .catch(error => {
            console.error('Error creating route:', error);
            Swal.fire('Ошибка', 'Не удалось создать маршрут', 'error');
        });
    }

    // Обновляем функцию viewRoute
    function viewRoute(routeId) {
        fetch(`/api/routes/${routeId}/view`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Очищаем текущий маршрут если есть
            selectedMarkers = [];
            if (routeLine) {
                map.removeLayer(routeLine);
                routeLine = null;
            }

            // Находим существующие маркеры на карте, которые входят в маршрут
            data.markers.forEach(routeMarker => {
                let existingMarker;
                markersGroup.eachLayer((layer) => {
                    if (layer.getLatLng().lat === routeMarker.latitude &&
                        layer.getLatLng().lng === routeMarker.longitude) {
                        existingMarker = layer;
                    }
                });

                const markerData = {
                    id: routeMarker.id,
                    title: routeMarker.title,
                    description: routeMarker.description,
                    latitude: routeMarker.latitude,
                    longitude: routeMarker.longitude,
                    leafletMarker: existingMarker
                };

                selectedMarkers.push(markerData);
            });

            // Остальной код без изменений...
            routeControl.addClass('active');
            $('#routesList').addClass('hidden');
            isRouteCreationMode = true;

            routeName.val(data.name);
            routeDescription.val(data.description);

            updateSelectedMarkersList();
            updateRouteLine();

            if (selectedMarkers.length > 0) {
                const bounds = L.latLngBounds(selectedMarkers.map(m => [m.latitude, m.longitude]));
                map.fitBounds(bounds, { padding: [50, 50] });
            }

            routeControl.data('editing-route-id', routeId);

            createRouteBtn
                .text('Сохранить изменения')
                .off('click')
                .on('click', function() {
                    updateRoute(routeId, {
                        name: routeName.val(),
                        description: routeDescription.val(),
                        markers: selectedMarkers.map(m => m.id)
                    });
                });

            // Изменяем заголовок
            $('#routeControlTitle').text('Редактирование маршрута');

            // Обновляем иконки маркеров с номерами
            updateMarkerIcons();
        })
        .catch(error => {
            console.error('Error loading route:', error);
            Swal.fire('Ошибка', 'Не удалось загрузить маршрут', 'error');
        });
    }

    // Функция обновления маршрута
    function updateRoute(routeId, routeData) {
        fetch(`/api/routes/${routeId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(routeData)
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire('Успешно', 'Маршрут обновлен', 'success').then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            console.error('Error updating route:', error);
            Swal.fire('Ошибка', 'Не удалось обновить маршрут', 'error');
        });
    }
</script>
@endpush
