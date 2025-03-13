@extends('template.index')

@section('title', 'Прохождение теста')

@section('content')
<!-- Добавим модальное окно для результатов -->
<div class="modal fade" id="resultModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Результаты теста</h5>
            </div>
            <div class="modal-body" id="resultContainer">
                <!-- Сюда будут добавлены результаты -->
            </div>
        </div>
    </div>
</div>

<div class="container-fluid"
     x-data="{
         currentStep: 1,
         progress: 0,
         totalQuestions: {{ count($test->questions) }},

         init() {
             this.updateProgress();
         },

         nextStep() {
             if (this.currentStep < this.totalQuestions) {
                 this.currentStep++;
                 this.updateProgress();
             }
         },

         prevStep() {
             if (this.currentStep > 1) {
                 this.currentStep--;
                 this.updateProgress();
             }
         },

         updateProgress() {
             this.progress = Math.round((this.currentStep - 1) * 100 / this.totalQuestions);
         },

         async submitTest() {
             const form = document.getElementById('testForm');
             const formData = new FormData(form);

             try {
                 const response = await fetch('{{ route("user.tests.submit", $test) }}', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         'Accept': 'application/json',
                     },
                     body: new URLSearchParams(formData)
                 });

                 if (!response.ok) {
                     throw new Error('Ошибка при отправке теста');
                 }

                 const data = await response.json();

                 Swal.fire({
                     title: 'Тест завершен!',
                     text: `Ваш результат: ${data.score}%`,
                     icon: 'success'
                 });

             } catch (error) {
                 Swal.fire({
                     icon: 'error',
                     title: 'Ошибка!',
                     text: error.message
                 });
             }
         }
     }">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-sm text-muted mb-1" x-text="'Вопрос ' + currentStep + ' из {{ count($test->questions) }}'"></div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success"
                             role="progressbar"
                             x-bind:style="'width: ' + progress + '%'"
                             x-text="progress + '%'">
                        </div>
                    </div>

                    <form id="testForm" @submit.prevent="submitTest">
                        @csrf
                        @foreach($test->questions as $index => $question)
                        <div x-show.important="currentStep === {{ $loop->iteration }}">
                            <h5 class="mb-3">{{ $question->question_text }}</h5>
                            <div class="options-list">
                                @foreach($question->options as $option)
                                <div class="form-check mb-3">
                                    <input class="form-check-input"
                                           type="{{ $question->is_multiple ? 'checkbox' : 'radio' }}"
                                           name="answers[{{ $question->id }}]{{ $question->is_multiple ? '[]' : '' }}"
                                           value="{{ $option }}"
                                           id="option_{{ $question->id }}_{{ $loop->index }}">
                                    <label class="form-check-label"
                                           for="option_{{ $question->id }}_{{ $loop->index }}">
                                        {{ $option }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <!-- Навигация -->
                        <div class="d-flex mt-4">
                            <button type="button"
                                    class="btn btn-secondary"
                                    x-show.important="currentStep > 1"
                                    @click="prevStep">
                                Назад
                            </button>

                            <button type="button"
                                    class="btn btn-primary ms-auto"
                                    x-show.important="currentStep < totalQuestions"
                                    @click="nextStep">
                                Далее
                            </button>

                            <button type="submit"
                                    class="btn btn-success ms-auto"
                                    x-show.important="currentStep === totalQuestions">
                                Завершить тест
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('page-scripts')
<style>
[x-cloak] { display: none !important; }

.rating {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.rating-star {
    cursor: pointer;
    color: #ccc;
    font-size: 1.5rem;
    transition: color 0.2s;
}

.rating-star:hover,
.rating-star.active {
    color: #ffc107;
}

.modal-backdrop {
    opacity: 0.7;
}
</style>

<script>
// Удалить или закомментировать этот блок
/*
document.addEventListener('alpine:init', () => {
    Alpine.data('testApp', () => ({
        currentStep: 1,
        progress: 0,
        totalQuestions: {{ count($test->questions) }},

        init() {
            this.updateProgress();
        },

        nextStep() {
            if (this.currentStep < this.totalQuestions) {
                this.currentStep++;
                this.updateProgress();
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.updateProgress();
            }
        },

        updateProgress() {
            this.progress = Math.round((this.currentStep - 1) * 100 / this.totalQuestions);
        },

        async submitTest() {
            const form = document.getElementById('testForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('{{ route("user.tests.submit", $test) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: new URLSearchParams(formData)
                });

                if (!response.ok) {
                    throw new Error('Ошибка при отправке теста');
                }

                const data = await response.json();

                // Показываем результаты
                Swal.fire({
                    title: 'Тест завершен!',
                    text: `Ваш результат: ${data.score}%`,
                    icon: 'success'
                });

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка!',
                    text: error.message
                });
            }
        }
    }));
});
*/

function rateTest(rating) {
    fetch('{{ route("user.tests.rate", $test) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ rating: rating })
    })
    .then(response => response.json())
    .then(data => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: 'success',
            title: 'Спасибо за оценку!'
        });

        // Сначала сбрасываем все звезды
        document.querySelectorAll('.rating-star').forEach(star => {
            star.classList.remove('ri-star-fill', 'ri-star-line', 'active');
            star.classList.add('ri-star-line');
        });

        // Затем заполняем до выбранной оценки
        document.querySelectorAll('.rating-star').forEach(star => {
            const starRating = parseInt(star.dataset.rating);
            if (starRating <= rating) {
                star.classList.remove('ri-star-line');
                star.classList.add('ri-star-fill', 'active');
            }
        });
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Ошибка!',
            text: 'Не удалось сохранить оценку'
        });
    });
}

function finishTest() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('resultModal'));
    if (modal) {
        modal.hide();
        window.location.href = '{{ route("user.tests.index") }}';
    }
}

function highlightStars(rating) {
    // Сначала возвращаем активным звездам их состояние
    document.querySelectorAll('.rating-star').forEach(star => {
        if (star.classList.contains('active')) {
            star.classList.remove('ri-star-line');
            star.classList.add('ri-star-fill');
        } else {
            star.classList.remove('ri-star-fill');
            star.classList.add('ri-star-line');
        }
    });

    // Затем подсвечиваем до текущей позиции курсора
    document.querySelectorAll('.rating-star').forEach(star => {
        const starRating = parseInt(star.dataset.rating);
        if (starRating <= rating) {
            star.classList.remove('ri-star-line');
            star.classList.add('ri-star-fill');
        }
    });
}

function resetStars() {
    // Возвращаем первоначальное состояние
    document.querySelectorAll('.rating-star').forEach(star => {
        if (star.classList.contains('active')) {
            star.classList.remove('ri-star-line');
            star.classList.add('ri-star-fill');
        } else {
            star.classList.remove('ri-star-fill');
            star.classList.add('ri-star-line');
        }
    });
}
</script>
@endpush

