@extends('template.index')

@section('title', 'Управление картой')
@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-header bg-transparent">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Управление картой</h6>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Правая колонка - карта -->
                            <div class="col-md-12">
                                <div class="card border shadow-none radius-10">
                                    <div class="card-body p-0">
                                        <div id="map" style="height: 600px !important; width: 100% !important; border-radius: 10px;"></div>
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


@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="{{ asset('css/map.css') }}">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/@mapbox/leaflet-pip@latest/leaflet-pip.js"></script>
<script>
// Объявляем глобальные переменные
let map, markersGroup, belarusLayer, tempMarker;
        const customIcon = L.divIcon({
            html: '<i class="ri-map-pin-2-fill" style="font-size: 24px; color: #2c3e50;"></i>',
            className: 'custom-div-icon',
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -30]
        });

document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Map element not found');
        return;
    }

    try {
        // Инициализация карты
        map = L.map('map', {
            center: [53.9, 27.5667],
            zoom: 6,
            minZoom: 6,
            maxZoom: 15
        });

        // Добавляем тайлы карты
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Инициализируем группу маркеров
        markersGroup = L.featureGroup().addTo(map);

        // Загружаем границы Беларуси
        loadBelarusBorders();

        // Загружаем существующие маркеры
        loadExistingMarkers();

        // Добавляем обработчики событий
        setupEventListeners();

    } catch (error) {
        console.error('Error initializing map:', error);
    }
}

// Функция для создания маркера
function createMarker(data, isTemp = false) {
    const marker = L.marker([data.latitude, data.longitude], { icon: customIcon });

    if (!isTemp) {
        // Создаем data-атрибуты для хранения данных маркера
        const popupContent = `
            <div class="marker-popup">
                <b>${data.title}</b><br>
                ${data.description}<br><br>
                <button class="btn btn-sm btn-light edit-marker"
                    data-marker-id="${data.id}"
                    data-title="${data.title.replace(/"/g, '&quot;')}"
                    data-description="${data.description.replace(/"/g, '&quot;')}"
                    data-latitude="${data.latitude}"
                    data-longitude="${data.longitude}"
                    onclick="editMarkerFromButton(this)">
                    <i class="ri-edit-line"></i> Редактировать
                </button>
                <button class="btn btn-sm btn-light move-marker"
                    data-marker-id="${data.id}"
                    onclick="moveMarkerFromButton(this)">
                    <i class="ri-drag-move-line"></i> Переместить
                </button>
                <button class="btn btn-sm btn-light-danger delete-marker"
                    data-marker-id="${data.id}"
                    onclick="deleteMarkerFromButton(this)">
                    <i class="ri-delete-bin-line"></i> Удалить
                </button>
            </div>
        `;

        marker
            .bindPopup(popupContent)
            .bindTooltip(data.title, {
                permanent: true,
                direction: 'top',
                offset: [0, -35],
                className: 'marker-tooltip marker-tooltip-name'
            });
    }

    return marker;
}

// Функция сохранения маркера
function saveMarker(lat, lng, title, description) {
    fetch('/api/markers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            latitude: lat,
            longitude: lng,
            title: title,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (tempMarker) {
            map.removeLayer(tempMarker);
        }
        const marker = createMarker(data);
        markersGroup.addLayer(marker);
    });
}

// Функция редактирования маркера
function editMarker(markerId, markerData, buttonElement) {
    // Получаем маркер через поиск в markersGroup
    let marker;
    markersGroup.eachLayer((layer) => {
        if (layer.getLatLng().lat === markerData.latitude && layer.getLatLng().lng === markerData.longitude) {
            marker = layer;
        }
    });

    if (!marker) {
        console.error('Маркер не найден');
        return;
    }

    Swal.fire({
        title: 'Редактировать маркер',
        html: `
            <input id="marker_title" class="swal2-input" placeholder="Название" value="${markerData.title}">
            <textarea id="marker_description" class="swal2-textarea" placeholder="Описание">${markerData.description}</textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Сохранить',
        cancelButtonText: 'Отмена',
        preConfirm: () => {
            const title = document.getElementById('marker_title').value;
            const description = document.getElementById('marker_description').value;
            if (!title || !description) {
                Swal.showValidationMessage('Заполните все поля');
                return false;
            }
            return { title, description };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const updatedData = {
                ...markerData,
                title: result.value.title,
                description: result.value.description
            };

            fetch(`/api/markers/${markerId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(updatedData)
            })
            .then(response => response.json())
            .then(data => {
                marker.setPopupContent(createMarker(data).getPopup().getContent());
                marker.setTooltipContent(data.title);
                Swal.fire('Успешно', 'Маркер обновлен', 'success');
            });
        }
    });
}

// Функция удаления маркера
function deleteMarker(markerId, buttonElement) {
    // Получаем маркер через поиск в markersGroup
    let marker;
    markersGroup.eachLayer((layer) => {
        const popup = layer.getPopup();
        if (popup && popup.getContent().includes(`data-marker-id="${markerId}"`)) {
            marker = layer;
        }
    });

    if (!marker) {
        console.error('Маркер не найден');
        return;
    }

    Swal.fire({
        title: 'Подтверждение',
        text: 'Вы уверены, что хотите удалить этот маркер?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/markers/${markerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(() => {
                markersGroup.removeLayer(marker);
                Swal.fire('Успешно', 'Маркер удален', 'success');
            });
        }
    });
}

// Новые функции-обертки для обработки кликов по кнопкам
function editMarkerFromButton(button) {
    const markerData = {
        id: button.dataset.markerId,
        title: button.dataset.title,
        description: button.dataset.description,
        latitude: parseFloat(button.dataset.latitude),
        longitude: parseFloat(button.dataset.longitude)
    };
    editMarker(markerData.id, markerData, button);
}

function deleteMarkerFromButton(button) {
    const markerId = button.dataset.markerId;
    deleteMarker(markerId, button);
}

// Добавим новые функции для перемещения маркера
function moveMarkerFromButton(button) {
    const markerId = button.dataset.markerId;
    let marker;
    let originalPosition;
    let coordsDisplay;

    // Находим маркер
    markersGroup.eachLayer((layer) => {
        const popup = layer.getPopup();
        if (popup && popup.getContent().includes(`data-marker-id="${markerId}"`)) {
            marker = layer;
        }
    });

    if (!marker) {
        console.error('Маркер не найден');
        return;
    }

    // Сохраняем исходную позицию
    originalPosition = marker.getLatLng();

    // Закрываем попап
    marker.closePopup();

    // Создаем элемент для отображения координат
    coordsDisplay = L.control({position: 'bottomleft'});
    coordsDisplay.onAdd = function () {
        const div = L.DomUtil.create('div', 'coords-display');
        div.style.background = 'white';
        div.style.padding = '5px 10px';
        div.style.border = '2px solid rgba(0,0,0,0.2)';
        div.style.borderRadius = '4px';
        div.style.fontSize = '14px';
        div.innerHTML = `Координаты: ${marker.getLatLng().lat.toFixed(6)}, ${marker.getLatLng().lng.toFixed(6)}`;
        return div;
    };
    coordsDisplay.addTo(map);

    // Обновляем координаты при перемещении
    marker.on('drag', function(e) {
        const latlng = marker.getLatLng();
        coordsDisplay.getContainer().innerHTML = `Координаты: ${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`;
    });

    // Делаем маркер перемещаемым
    marker.dragging.enable();

    // Показываем toast-уведомление
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Сохранить',
        cancelButtonText: 'Отмена',
        timer: false,
        timerProgressBar: false,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'info',
        title: 'Перетащите маркер в новое место'
    }).then((result) => {
        // Удаляем отображение координат
        map.removeControl(coordsDisplay);

        if (result.isConfirmed) {
            const newLatLng = marker.getLatLng();

            // Проверяем, находится ли новая позиция в пределах Беларуси
            const isInBelarus = leafletPip.pointInLayer(
                [newLatLng.lng, newLatLng.lat],
                belarusLayer,
                true
            ).length > 0;

            if (!isInBelarus) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка',
                    text: 'Маркер можно установить только на территории Беларуси'
                });
                marker.setLatLng(originalPosition);
            } else {
                // Сохраняем новую позицию
                const updatedData = {
                    title: button.dataset.title,
                    description: button.dataset.description,
                    latitude: newLatLng.lat,
                    longitude: newLatLng.lng
                };

                fetch(`/api/markers/${markerId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(updatedData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Обновляем данные маркера
                    button.dataset.latitude = newLatLng.lat;
                    button.dataset.longitude = newLatLng.lng;

                    Toast.fire({
                        icon: 'success',
                        title: 'Позиция маркера обновлена',
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .catch(error => {
                    console.error('Error updating marker:', error);
                    marker.setLatLng(originalPosition);
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка',
                        text: 'Не удалось сохранить новую позицию: ' + error.message
                    });
                });
            }
        } else {
            // Возвращаем маркер на исходную позицию при отмене
            marker.setLatLng(originalPosition);
        }

        // Отключаем перемещение маркера
        marker.dragging.disable();
        // Удаляем обработчик события перемещения
        marker.off('drag');
    });
}

function loadBelarusBorders() {
    fetch('/js/belarus.geojson')
            .then(response => response.json())
            .then(data => {
                belarusLayer = L.geoJSON(data, {
                    style: {
                        color: '#2c3e50',
                        weight: 2,
                        fillOpacity: 0.1
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
                data.markers.forEach(marker => {
                const markerObj = createMarker(marker);
                markersGroup.addLayer(markerObj);
            });
        });
}

function setupEventListeners() {
        // Обработчик клика по карте
        map.on('click', function(e) {
            if (!belarusLayer) return;

            const isInBelarus = leafletPip.pointInLayer([e.latlng.lng, e.latlng.lat], belarusLayer, true).length > 0;

            if (!isInBelarus) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка',
                    text: 'Маркер можно установить только на территории Беларуси'
                });
                return;
            }

            if (tempMarker) {
                map.removeLayer(tempMarker);
            }

            tempMarker = L.marker(e.latlng, { icon: customIcon }).addTo(map);

            Swal.fire({
                title: 'Новый маркер',
                html: `
                    <input id="marker_title" class="swal2-input" placeholder="Название">
                    <textarea id="marker_description" class="swal2-textarea" placeholder="Описание"></textarea>
                    <div class="swal2-input-group">
                        <label for="marker_lat">Широта:</label>
                        <input type="text" id="marker_lat" class="swal2-input" value="${e.latlng.lat.toFixed(6)}" readonly>
                    </div>
                    <div class="swal2-input-group">
                        <label for="marker_lng">Долгота:</label>
                        <input type="text" id="marker_lng" class="swal2-input" value="${e.latlng.lng.toFixed(6)}" readonly>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Сохранить',
                cancelButtonText: 'Отмена',
                preConfirm: () => {
                    const title = document.getElementById('marker_title').value;
                    const description = document.getElementById('marker_description').value;
                    const lat = document.getElementById('marker_lat').value;
                    const lng = document.getElementById('marker_lng').value;
                    if (!title || !description) {
                        Swal.showValidationMessage('Заполните все поля');
                        return false;
                    }
                    return { title, description, lat, lng };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    saveMarker(result.value.lat, result.value.lng, result.value.title, result.value.description);
                } else {
                    map.removeLayer(tempMarker);
                    tempMarker = null;
                }
            });
        });

        // Обработчик изменения зума
        map.on('zoomend', function() {
            const currentZoom = map.getZoom();
            markersGroup.eachLayer(function(layer) {
                let tooltip = layer.getTooltip();
                console.log(tooltip);
                if (tooltip) {
                    if (currentZoom >= 8) {
                        layer.openTooltip();
                    } else {
                        layer.closeTooltip();
                    }
                }
            });
    });
}

</script>
@endpush
