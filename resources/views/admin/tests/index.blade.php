@extends('template.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Управление тестами</h4>
                <button onclick="showCreateTestModal()" class="btn btn-primary">Создать тест</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Количество вопросов</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tests as $test)
                                <tr>
                                    <td>{{ $test->id }}</td>
                                    <td>{{ $test->title }}</td>
                                    <td>{{ $test->questions->count() }}</td>
                                    <td>
                                        @if($test->is_active)
                                            <span class="badge bg-success">Активен</span>
                                        @else
                                            <span class="badge bg-danger">Неактивен</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editTest({{ $test->id }})">
                                            Редактировать
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteTest({{ $test->id }})">
                                            Удалить
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
let questionCounter = 0;

function showCreateTestModal(isEdit = false, testId = null) {
    // Сбрасываем счетчик вопросов
    questionCounter = 0;

    Swal.fire({
        title: isEdit ? 'Редактирование теста' : 'Создание теста',
        html: `
            <form id="createTestForm" class="text-start">
                @csrf
                ${isEdit ? `
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="test_id" value="${testId}">
                ` : ''}
                <div class="mb-3">
                    <label class="form-label">Название теста</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div id="questions-container">
                    <!-- Вопросы будут добавляться сюда -->
                </div>
                <button type="button" class="btn btn-info btn-sm mt-3" onclick="addQuestion()">
                    Добавить вопрос
                </button>
            </form>
        `,
        width: '800px',
        showCancelButton: true,
        confirmButtonText: isEdit ? 'Сохранить' : 'Создать',
        cancelButtonText: 'Отмена',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return submitTestForm(isEdit, testId);
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then(() => {
        // Добавляем первый вопрос автоматически только при создании нового теста
        if (!isEdit) {
            setTimeout(() => {
                addQuestion();
            }, 100);
        }
    });
}

function addQuestion() {
    const container = document.getElementById('questions-container');

    // Обновляем номера всех существующих вопросов
    container.querySelectorAll('.card-title').forEach((title, index) => {
        title.textContent = `Вопрос ${index + 1}`;
    });

    const questionDiv = document.createElement('div');
    questionDiv.className = 'card mb-3';
    questionDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="card-title">Вопрос ${container.children.length + 1}</h6>
                ${container.children.length > 0 ? '<button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">×</button>' : ''}
            </div>

            <div class="mb-2">
                <label class="form-label">Текст вопроса</label>
                <textarea class="form-control" name="questions[${questionCounter}][question_text]" required></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label">Тип ответа</label>
                <select class="form-control" name="questions[${questionCounter}][is_multiple]" required>
                    <option value="0">Один правильный ответ</option>
                    <option value="1">Несколько правильных ответов</option>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Баллы</label>
                <input type="number" class="form-control" name="questions[${questionCounter}][points]" value="1" min="1" required>
            </div>

            <div class="options-container">
                <label class="form-label">Варианты ответов</label>
                <div class="options-list" data-question="${questionCounter}">
                    <!-- Опции будут добавляться сюда -->
                </div>
                <button type="button" class="btn btn-info btn-sm mt-2" onclick="addOption(${questionCounter})">
                    Добавить вариант
                </button>
            </div>
        </div>
    `;
    container.appendChild(questionDiv);

    // Добавляем два варианта ответа по умолчанию
    addOption(questionCounter);
    addOption(questionCounter);

    questionCounter++;
}

function addOption(questionIndex) {
    const optionsList = document.querySelector(`.options-list[data-question="${questionIndex}"]`);
    const optionItem = document.createElement('div');
    optionItem.className = 'input-group mb-2';
    optionItem.innerHTML = `
        <input type="text" class="form-control" name="questions[${questionIndex}][options][]" required>
        <div class="input-group-text">
            <input type="checkbox" name="questions[${questionIndex}][correct_answers][]" onchange="updateCheckboxValue(this)">
        </div>
        <button type="button" class="btn btn-danger" onclick="removeOption(this)">×</button>
    `;
    optionsList.appendChild(optionItem);
}

function removeQuestion(button) {
    const container = document.getElementById('questions-container');
    button.closest('.card').remove();

    // Обновляем номера оставшихся вопросов
    container.querySelectorAll('.card-title').forEach((title, index) => {
        title.textContent = `Вопрос ${index + 1}`;
    });
}

function removeOption(button) {
    const optionsList = button.closest('.options-list');
    if (optionsList.children.length > 2) {
        button.closest('.input-group').remove();
    } else {
        Swal.fire('Ошибка', 'Должно быть минимум 2 варианта ответа', 'error');
    }
}

function updateCheckboxValue(checkbox) {
    const input = checkbox.closest('.input-group').querySelector('input[type="text"]');
    checkbox.value = input.value;
}

async function submitTestForm(isEdit = false, testId = null) {
    const form = document.getElementById('createTestForm');
    const formData = new FormData(form);

    try {
        const url = isEdit
            ? `{{ url('admin/tests') }}/${testId}`
            : '{{ route("admin.tests.store") }}';

        const response = await fetch(url, {
            method: isEdit ? 'POST' : 'POST', // POST для обоих случаев из-за загрузки файлов
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: new URLSearchParams(formData)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Произошла ошибка');
        }

        await Swal.fire({
            icon: 'success',
            title: 'Успешно!',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
        });

        window.location.href = data.redirect;
        return false;

    } catch (error) {
        Swal.showValidationMessage(error.message);
        return false;
    }
}

function deleteTest(testId) {
    Swal.fire({
        title: 'Подтверждение',
        text: 'Вы уверены, что хотите удалить этот тест?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`{{ url('admin/tests') }}/${testId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Произошла ошибка при удалении');
                }
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Успешно!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка!',
                    text: error.message
                });
            });
        }
    });
}

function editTest(testId) {
    // Загружаем данные теста
    fetch(`/admin/tests/${testId}/edit`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(test => {
        questionCounter = 0;
        showCreateTestModal(true, testId);

        // Заполняем форму данными после небольшой задержки, чтобы модальное окно успело создаться
        setTimeout(() => {
            const form = document.getElementById('createTestForm');
            form.querySelector('[name="title"]').value = test.title;
            form.querySelector('[name="description"]').value = test.description;

            // Добавляем существующие вопросы
            test.questions.forEach(question => {
                addQuestion();
                const questionDiv = document.querySelector(`[name="questions[${questionCounter-1}][question_text]"]`).closest('.card');
                questionDiv.querySelector('[name$="[question_text]"]').value = question.question_text;
                questionDiv.querySelector('[name$="[is_multiple]"]').value = question.is_multiple ? "1" : "0";
                questionDiv.querySelector('[name$="[points]"]').value = question.points;

                // Очищаем список опций
                const optionsList = questionDiv.querySelector('.options-list');
                optionsList.innerHTML = '';

                // Добавляем существующие опции
                question.options.forEach(option => {
                    const isCorrect = question.correct_answers.includes(option);
                    addOption(questionCounter-1);
                    const lastOption = optionsList.lastElementChild;
                    lastOption.querySelector('input[type="text"]').value = option;
                    lastOption.querySelector('input[type="checkbox"]').checked = isCorrect;
                    if (isCorrect) {
                        lastOption.querySelector('input[type="checkbox"]').value = option;
                    }
                });
            });
        }, 100);
    });
}

// Обработка сообщений об успехе/ошибке
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Успешно!',
        text: '{{ session("success") }}',
        showConfirmButton: false,
        timer: 2000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Ошибка!',
        text: '{{ session("error") }}'
    });
@endif

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Ошибка!',
        text: '{{ $errors->first() }}'
    });
@endif
</script>
