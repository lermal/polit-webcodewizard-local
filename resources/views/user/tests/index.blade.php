@extends('template.index')

@section('title', 'Доступные тесты')

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('testApp', () => ({
        currentStep: 1,
        progress: 25,

        nextStep() {
            if (this.currentStep < 4) {
                this.currentStep++;
                this.progress = this.currentStep * 25;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.progress = this.currentStep * 25;
            }
        }
    }));
});
</script>
@endpush

@section('content')
<div class="container-fluid" x-data="testApp">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Доступные тесты</h4>
            </div>
        </div>
    </div>

    <div class="row">
        @if($tests->isEmpty())
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Нет доступных тестов</h5>
                        <p class="text-muted">В данный момент нет активных тестов для прохождения</p>
                    </div>
                </div>
            </div>
        @else
            @foreach($tests as $test)
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $test->title }}</h5>
                        <p class="card-text">{{ $test->description }}</p>
                        <div class="mb-3">
                            <span class="badge bg-info">Вопросов: {{ $test->questions_count }}</span>
                            <span class="badge bg-success">{{ $test->is_active ? 'Активен' : 'Неактивен' }}</span>
                            @if($test->averageRating() > 0)
                                <div class="mt-2">
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="ri-star-{{ $i <= round($test->averageRating()) ? 'fill' : 'line' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">Средняя оценка: {{ number_format($test->averageRating(), 1) }}</small>
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('user.tests.show', $test) }}" class="btn btn-primary">
                            Пройти тест
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
