@extends('template.index')

@section('title', 'Статистика')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Статистика</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0 mb-3">Всего пользователей</h5>
                            <h3 class="mt-3 mb-3">{{ $usersCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">График активности</h4>
                    <div id="activity-chart" class="apex-charts" style="min-height: 375px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/admin/pages/stats.js') }}"></script>
@endpush
