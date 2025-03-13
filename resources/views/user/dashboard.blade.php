@extends('template.index')
@php
    use Carbon\Carbon;
@endphp

@section('title', 'Личный кабинет')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Личный кабинет</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Основная информация -->
        <div class="col-xl-4">
            <div class="card" style="height: 93%;">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Профиль</h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" class="rounded-circle avatar-lg">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mt-0 mb-1">{{ auth()->user()->name }}</h4>
                            <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                            <p class="text-muted mb-0">Роль: {{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="ri-edit-box-line"></i> Редактировать профиль
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика активности -->
        <div class="col-xl-4">
            <div class="card" style="height: 93%;">
                <div class="card-body">
                    <h5 class="card-title mb-3">Активность</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">Дата регистрации</h6>
                        <p class="mb-0">{{ Carbon::parse(auth()->user()->getRawOriginal('created_at'))->timezone('Europe/Moscow')->format('d.m.Y H:i') }}</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">Последний вход</h6>
                        <p class="mb-0">{{ auth()->user()->getRawOriginal('last_login') ? Carbon::parse(auth()->user()->getRawOriginal('last_login'))->timezone('Europe/Moscow')->format('d.m.Y H:i') : 'Нет данных' }}</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-muted mb-0">Статус</h6>
                        <span class="badge bg-success">Активен</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Прогресс обучения -->
        <div class="col-xl-4">
            <div class="card" style="height: 93%;">
                <div class="card-body">
                    <h5 class="card-title mb-3">Прогресс обучения</h5>
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Общий прогресс</h6>
                        @php
                            $totalTests = \App\Models\Test::where('is_active', true)->count();
                            $completedTests = auth()->user()->testResults()->distinct('test_id')->count();
                            $progressPercentage = $totalTests > 0 ? round(($completedTests / $totalTests) * 100) : 0;
                            $averageScore = auth()->user()->testResults()->avg('percentage') ?? 0;
                        @endphp
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar bg-success"
                                 role="progressbar"
                                 style="width: {{ $progressPercentage }}%;"
                                 aria-valuenow="{{ $progressPercentage }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">{{ $progressPercentage }}%</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">Пройдено тестов</h6>
                        <p class="mb-0">{{ $completedTests }} из {{ $totalTests }}</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-muted mb-0">Средний балл</h6>
                        <p class="mb-0">{{ number_format($averageScore, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Последние действия -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Последние действия</h5>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Действие</th>
                                    <th>Результат</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ Carbon::now()->timezone('Europe/Moscow')->format('d.m.Y H:i') }}</td>
                                    <td>Вход в систему</td>
                                    <td><span class="badge bg-success">Успешно</span></td>
                                </tr>
                                <!-- Здесь будут другие действия -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования профиля -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать профиль</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="mb-3">
                        <label class="form-label">Имя</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Новый пароль</label>
                        <input type="password" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>
@endsection
