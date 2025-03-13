@extends('template.index')

@section('title', 'Настройки сайта')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Настройки сайта</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="settings-form">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Название сайта</label>
                                <input type="text" class="form-control" name="site_name" value="{{ config('app.name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email администратора</label>
                                <input type="email" class="form-control" name="admin_email" value="{{ config('mail.from.address') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/admin/pages/settings.js') }}"></script>
@endpush
