@extends('template.index')

@section('title', 'Пользователи')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Пользователи</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Имя</th>
                                    <th>Email</th>
                                    <th>Роль</th>
                                    <th>Группа</th>
                                    <th>Дата регистрации</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования -->
<div id="edit-user-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Редактировать пользователя</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-user-form">
                    <input type="hidden" id="edit-user-id">
                    <div class="mb-3">
                        <label class="form-label">Имя</label>
                        <input type="text" class="form-control" id="edit-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Роль</label>
                        <select class="form-select" id="edit-role">
                            <option value="user">Ученик</option>
                            <option value="teacher">Учитель</option>
                            <option value="admin">Администратор</option>
                        </select>
                    </div>
                    <div class="mb-3 teacher-only" style="display: none;">
                        <label class="form-label">Управление группами</label>
                        <div class="groups-container">
                            <div class="existing-groups mb-2">
                                <!-- Существующие группы будут добавлены через JS -->
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="add-group">
                                <i class="ri-add-line"></i> Создать группу
                            </button>
                        </div>
                    </div>
                    <div class="mb-3 student-only" style="display: none;">
                        <label class="form-label">Группа</label>
                        <select class="form-select" id="edit-group">
                            <option value="">Не выбрано</option>
                            <!-- Группы будут добавлены через JS -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="save-user">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно создания группы -->
<div id="create-group-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Создать группу</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create-group-form">
                    <div class="mb-3">
                        <label class="form-label">Название группы</label>
                        <input type="text" class="form-control" id="group-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" id="group-description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="save-group">Создать</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .group-item {
        display: flex;
        align-items: center;
        padding: 8px;
        background: #f8f9fa;
        margin-bottom: 5px;
        border-radius: 4px;
    }

    .group-item .group-name {
        flex-grow: 1;
    }

    .group-item .group-actions {
        display: flex;
        gap: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Инициализация DataTables
    const table = $('#users-table').DataTable({
        ajax: '/api/users',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'role' },
            { data: 'group' },
            { data: 'created_at' },
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-primary edit-user" data-id="${data.id}">
                            <i class="ri-edit-line"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']]
    });

    // Обработчик изменения роли
    $('#edit-role').change(function() {
        const role = $(this).val();
        if (role === 'teacher') {
            $('.teacher-only').show();
            $('.student-only').hide();
        } else if (role === 'user') {
            $('.teacher-only').hide();
            $('.student-only').show();
        } else {
            $('.teacher-only, .student-only').hide();
        }
    });

    // Загрузка данных пользователя для редактирования
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');

        $.get(`/api/users/${userId}`)
            .done(function(response) {
                $('#edit-user-id').val(response.id);
                $('#edit-name').val(response.name);
                $('#edit-email').val(response.email);
                $('#edit-role').val(response.role).trigger('change');

                if (response.role === 'teacher') {
                    loadTeacherGroups(response.id);
                } else if (response.role === 'user') {
                    loadAvailableGroups(response.group_id);
                }

                $('#edit-user-modal').modal('show');
            });
    });

    // Загрузка групп учителя
    function loadTeacherGroups(teacherId) {
        $.get(`/api/teachers/${teacherId}/groups`)
            .done(function(response) {
                const container = $('.existing-groups');
                container.empty();

                response.groups.forEach(group => {
                    container.append(`
                        <div class="group-item">
                            <span class="group-name">${group.name}</span>
                            <div class="group-actions">
                                <button class="btn btn-sm btn-light edit-group" data-id="${group.id}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-light-danger delete-group" data-id="${group.id}">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    `);
                });
            });
    }

    // Загрузка доступных групп для ученика
    function loadAvailableGroups(selectedGroupId) {
        $.get('/api/groups')
            .done(function(response) {
                const select = $('#edit-group');
                select.empty().append('<option value="">Не выбрано</option>');

                response.groups.forEach(group => {
                    select.append(`<option value="${group.id}" ${group.id === selectedGroupId ? 'selected' : ''}>
                        ${group.name}
                    </option>`);
                });
            });
    }

    // Создание новой группы
    $('#add-group').click(function() {
        $('#create-group-modal').modal('show');
    });

    // Сохранение группы
    $('#save-group').click(function() {
        const teacherId = $('#edit-user-id').val();
        const data = {
            name: $('#group-name').val(),
            description: $('#group-description').val(),
            teacher_id: teacherId
        };

        $.post('/api/groups', data)
            .done(function() {
                $('#create-group-modal').modal('hide');
                loadTeacherGroups(teacherId);
                Swal.fire('Успешно', 'Группа создана', 'success');
            })
            .fail(function(response) {
                Swal.fire('Ошибка', response.responseJSON.message || 'Не удалось создать группу', 'error');
            });
    });

    // Сохранение пользователя
    $('#save-user').click(function() {
        const userId = $('#edit-user-id').val();
        const data = {
            name: $('#edit-name').val(),
            email: $('#edit-email').val(),
            role: $('#edit-role').val()
        };

        if (data.role === 'user') {
            data.group_id = $('#edit-group').val();
        }

        $.ajax({
            url: `/api/users/${userId}`,
            method: 'PUT',
            data: data
        })
        .done(function() {
            $('#edit-user-modal').modal('hide');
            table.ajax.reload();
            Swal.fire('Успешно', 'Данные пользователя обновлены', 'success');
        })
        .fail(function(response) {
            Swal.fire('Ошибка', response.responseJSON.message || 'Не удалось обновить данные', 'error');
        });
    });

    // Удаление группы
    $(document).on('click', '.delete-group', function() {
        const groupId = $(this).data('id');
        const teacherId = $('#edit-user-id').val();

        Swal.fire({
            title: 'Подтверждение',
            text: 'Вы уверены, что хотите удалить эту группу?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Удалить',
            cancelButtonText: 'Отмена'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/groups/${groupId}`,
                    method: 'DELETE'
                })
                .done(function() {
                    loadTeacherGroups(teacherId);
                    Swal.fire('Успешно', 'Группа удалена', 'success');
                })
                .fail(function(response) {
                    Swal.fire('Ошибка', response.responseJSON.message || 'Не удалось удалить группу', 'error');
                });
            }
        });
    });
});
</script>
@endpush
