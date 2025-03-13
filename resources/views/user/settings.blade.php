@extends('template.index')

@section('title', 'Настройки профиля')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Настройки профиля</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="profile-settings-form" method="POST" action="{{ route('user.settings.update') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Имя</label>
                                <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Новый пароль</label>
                                <input type="password" class="form-control" name="password" placeholder="Оставьте пустым, если не хотите менять">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Подтверждение пароля</label>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Подтвердите новый пароль">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
$(document).ready(function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Успешно!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Ошибка!',
            text: '{{ session('error') }}',
            showConfirmButton: true
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Ошибка!',
            text: '{{ $errors->first() }}',
            showConfirmButton: true
        });
    @endif
});
</script>
@endpush
