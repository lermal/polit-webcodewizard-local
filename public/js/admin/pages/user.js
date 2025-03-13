$(document).ready(function () {

    const ADMIN_TOKEN = $('meta[name="admin-token"]').attr('content');

    // Добавление/изменение ника Instagram через AJAX при submit формы
    $('#instagramModal form').submit(function (event) {
        event.preventDefault(); // Предотвращаем стандартную отправку формы

        var instagramUsername = $('#instagramUsername').val();
        $.ajax({
            url: '/api/telegram-users/update-instagram', // Маршрут для обновления Instagram
            type: 'POST',
            data: {
                telegram_user_id: $('#user_id').val(),
                instagram: instagramUsername,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF-токен для безопасности
            },
            success: function (response) {
                $('#instagramModal').modal('hide');
                alert('Instagram успешно обновлен');
                location.reload();
            },
            error: function (response) {
                alert('Ошибка при обновлении Instagram: ' + response.responseJSON.message);
            }
        });
    });

    // Добавление/изменение реквизитов через AJAX при submit формы
    $('#addPropsModal form').submit(function (event) {
        event.preventDefault(); // Предотвращаем стандартную отправку формы

        var bank = $('#bankName').val() || null; // Если поле пустое, отправляем null
        var cardNumber = $('#cardNumber').val() || null; // Если поле пустое, отправляем null
        var sbpPhoneNumber = $('#sbpPhoneNumber').val() || null; // Если поле пустое, отправляем null
        var recipientName = $('#recipientName').val() || null; // Если поле пустое, отправляем null

        $.ajax({
            url: '/api/telegram-users/update-props', // Маршрут для обновления реквизитов
            type: 'POST',
            data: {
                telegram_user_id: $('#user_id').val(),
                bank: bank,
                card_number: cardNumber,
                sbp_phone_number: sbpPhoneNumber,
                recipient_name: recipientName,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF-токен для безопасности
            },
            success: function (response) {
                $('#addPropsModal').modal('hide');
                alert('Реквизиты успешно обновлены');
                location.reload();
            },
            error: function (response) {
                alert('Ошибка при обновлении реквизитов: ' + response.responseJSON.message);
            }
        });
    });

    // Добавление инвестиции через AJAX при submit формы
    $('#addInvestitionModal form').submit(function (event) {
        event.preventDefault();

        // Собираем данные формы
        var formData = {
            telegram_user_id: $('#user_id').val(),
            bank: $('#bankNameInvest').val(),
            card_number: $('#cardNumberInvest').val(),
            sbp_phone_number: $('#sbpPhoneNumberInvest').val(),
            recipient_name: $('#recipientNameInvest').val(),
            amount: $('#investmentAmount').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Показываем SweetAlert для подтверждения
        Swal.fire({
            title: 'Подтверждение',
            text: `Вы уверены, что хотите добавить инвестицию на сумму ${formData.amount}₽?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, добавить',
            cancelButtonText: 'Отмена',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Показываем индикатор загрузки
                Swal.fire({
                    title: 'Добавление инвестиции...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                console.log(formData);

                // Отправляем AJAX запрос
                $.ajax({
                    url: '/api/investments',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#addInvestitionModal').modal('hide');
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Инвестиция успешно добавлена',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function (response) {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Ошибка при добавлении инвестиции: ' + response.responseJSON.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    // Определяем функцию formatDetails в начале файла
    function formatDetails(data) {
        console.log(data);
        let details = '<div class="child-row-details collapsed">';
        details += `
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID ТГ:</strong> ${data.telegram_user_id}</p>
                <p><strong>Банк:</strong> ${data.bank}</p>
                <p><strong>Карта:</strong> ${data.card_number.replace(/(\d{4})(?=\d)/g, '$1 ')}</p>
                <p><strong>Дата создания:</strong> ${new Date(data.created_at).toLocaleString('ru-RU')}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Телефон:</strong> ${data.sbp_phone_number}</p>
                <p><strong>ФИО:</strong> ${data.recipient_name}</p>
                <p><strong>Всего проинвестировано:</strong> ${parseInt(data.total_invested).toLocaleString('ru-RU')} ₽</p>
                <p><strong>Доход:</strong> ${data.total_profit}%</p>
            </div>
        </div>
    `;
        return details;
    }

    // Добавьте функцию проверки мобильного устройства
    function isMobileDevice() {
        return window.innerWidth <= 1600; // Стандартная точка перелома для мобильных устройств
    }

    $(document).on('click', '#referrals_table tbody tr', function (event) {
        // Если это не мобильное устройство - игнорируем клик по строке
        if (!isMobileDevice()) {
            return;
        }

        if (!$(event.target).is('button, a')) {
            const table = $('#referrals_table').DataTable();
            const tr = $(this).closest('tr');
            const row = table.row(tr);

            if (row.child.isShown()) {
                const childRow = $(row.child());
                const details = childRow.find('.child-row-details');

                details.removeClass('expanded').addClass('collapsed');
                setTimeout(() => {
                    row.child.hide();
                    tr.removeClass('shown');
                }, 300);
            } else {
                // Закрываем все открытые строки
                table.rows().every(function () {
                    if (this.child.isShown()) {
                        const childRow = $(this.child());
                        const details = childRow.find('.child-row-details');

                        details.removeClass('expanded').addClass('collapsed');
                        setTimeout(() => {
                            this.child.hide();
                            $(this.node()).removeClass('shown');
                        }, 300);
                    }
                });

                // Открываем выбранную строку
                row.child(formatDetails(row.data()), 'child-row').show();
                tr.addClass('shown');

                setTimeout(() => {
                    const details = $(row.child()).find('.child-row-details');
                    details.addClass('expanded').removeClass('collapsed');
                }, 50);
            }
        }
    });

    // Обработка изменения радио-кнопок
    // Обработка переключения вкладок
    $("input[name='page_selector']").change(function () {
        var activeButton = $(this).attr('id');

        if ($.fn.DataTable.isDataTable('#history_table')) {
            $('#history_table').DataTable().clear().destroy();  // Очищаем и уничтожаем таблицу
        }

        if ($.fn.DataTable.isDataTable('#referrals_table')) {
            $('#referrals_table').DataTable().clear().destroy();  // Очищаем и уничтожаем таблицу
        }

        if ($.fn.DataTable.isDataTable('#investments_table')) {
            $('#investments_table').DataTable().clear().destroy();  // Очищаем и уничтожаем таблицу
        }

        // Скрываем все контентные блоки
        $('#user-info-content, #user-history-content, #user-referrals-content, #user-investments').addClass('d-none');

        let user_id = $('#user_id').val(); // Получаем ID пользователя
        // Показываем соответствующий контент в зависимости от выбранной вкладки
        if (activeButton === 'user_info') {
            $('#user-info-content').removeClass('d-none');
        } else if (activeButton === 'user_history') {
            $('#history_table').DataTable({
                ajax: {
                    url: '/api/telegram-users/get-user-history',  // Маршрут для истории
                    type: 'POST',
                    data: function (d) {
                        return JSON.stringify({ userId: user_id });
                    },
                    contentType: 'application/json',
                    dataSrc: function (json) {
                        if (json.transaction_count === 0) {
                            return [];
                        }
                        return json.transactions;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Error:', textStatus, errorThrown);
                        alert('Ошибка при получении транзакций.');
                    }
                },
                columns: [
                    {
                        data: 'created_at', render(data) {  // Дата создания
                            let date = new Date(data);
                            return date.toLocaleString('ru-RU', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                            });
                        }
                    },
                    {
                        data: 'reason',
                        render: function (data, type, row) {
                            // Проверяем значение reason и заменяем его на "Инвестиция"
                            if (data === 'investment-start') {
                                return 'Инвестиция';
                            } else if (data == 'investment-end') {
                                return 'Завершение инвестиции';
                            } else if (data == 'manual') {
                                return "Ручная выдача"
                            } else if (data == 'refferal-bouns') {
                                return "Бонус за друга"
                            } else if (data == 'bonus-payment') {
                                return "Выплата бонусов "
                            }
                            // Возвращаем оригинальное значение reason, если оно не равно 'investment_start'
                            return data;
                        }
                    },
                    {
                        data: 'amount',
                        render: function (data, type, row) {
                            if (row.type == 'withdrawal') {
                                return "- " + data + "₽";
                            } else if (row.type == 'deposit') {
                                return "+ " + data + "₽";
                            } else {
                                return data + "₽";
                            }
                        }
                    }
                ]
            });
            $('#user-history-content').removeClass('d-none');
        } else if (activeButton === 'user_referrals') {
            $('#referrals_table').DataTable({
                ajax: {
                    url: '/api/telegram-users/get-referrals',  // Убедитесь, что URL правильный
                    type: 'POST',
                    data: function (d) {
                        return JSON.stringify({ userId: user_id });  // Передаем ID пользователя в запросе
                    },
                    contentType: 'application/json',
                    dataSrc: function (json) {
                        // Проверка на наличие рефералов
                        if (json.friends.length === 0) {
                            return [];
                        }

                        return json.friends;  // Возвращаем список рефералов
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Error occurred:', textStatus, errorThrown);
                        console.error('Response:', jqXHR.responseText);
                        alert('An error occurred while fetching referrals. Check the console for details.');
                    }
                },
                columns: [
                    {
                        data: 'telegram_user_id',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return `<a href="/admin/users/${row.id}" class="text-primary">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'telegram_name',  // Логин пользователя
                    },
                    {
                        data: 'telegram_login',  // Имя пользователя
                    },
                    {
                        data: 'instagram',  // Всего TRX в активных играх
                    },
                    {
                        data: 'total_invested',  // Общая прибыль от игр
                    },
                    {
                        data: 'total_profit'
                    }
                ],
                processing: false,  // Показывать индикатор загрузки
                serverSide: false,  // Поскольку данные загружаются из JSON, серверная обработка не требуется
                paging: true,  // Включаем пагинацию
                searching: true,  // Включаем поиск по таблице
                ordering: true,  // Включаем сортировку
                order: [[0, 'asc']],  // Сортировка по имени
                responsive: {
                    details: {
                        type: 'column',
                        display: $.fn.dataTable.Responsive.display.childRow, // Изменяем способ отображения
                        renderer: function (api, rowIdx, columns) {
                            let data = '<div class="child-row-details collapsed">'; // Добавляем класс collapsed по умолчанию
                            columns.forEach(function (col) {
                                if (col.hidden || col.dataIndex === 5) {  // Всегда отображаем 5-ю колонку
                                    data += `<p><strong>${col.title}:</strong> ${col.data}</p>`;
                                }
                            });
                            data += '</div>';
                            return data;
                        }
                    }
                },
                dom: 'lBfrtip',
                buttons: [
                    'colvis'  // Добавляет кнопку для управления видимостью колонок
                ],
                language: {
                    responsive: {
                        details: {
                            type: 'column',
                            renderer: function (api, rowIdx, columns) {
                                let data = '';

                                columns.forEach(function (col) {
                                    if (col.hidden || col.dataIndex === 5) {  // Всегда отображаем 5-ю колонку
                                        data += `<tr>
                                            <td><strong>${col.title}:</strong></td>
                                            <td>${col.data}</td>
                                        </tr>`;
                                    }
                                });

                                return data ?
                                    `<table class="table table-striped">${data}</table>` :
                                    false;
                            }
                        }
                    },
                    lengthMenu: "Показать _MENU_ записей",
                    search: "Поиск:"
                }
            });
            $('#user-referrals-content').removeClass('d-none');
        } else if (activeButton === 'user_investments') {
            $('#investments_table').DataTable({
                ajax: {
                    url: '/api/telegram-users/get-investitions',
                    type: 'POST',
                    data: function (d) {
                        return JSON.stringify({ userId: user_id });
                    },
                    contentType: 'application/json',
                    dataSrc: function (json) {
                        if (json.investments.length === 0 || json.user.length === 0) {
                            return [];
                        }
                        return json.investments.map(function (investment) {
                            investment.user = json.user;
                            return investment;
                        });
                    }
                },
                columns: [
                    {
                        data: 'created_at',
                        render: function (data) {
                            let date = new Date(data);
                            return date.toLocaleString('ru-RU', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: 'user.telegram_user_id',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return `<a href="/admin/users/${row.user.id}" class="text-primary">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <div><strong>Банк:</strong> ${row.user.bank || 'Не уазан'}</div>
                                <div><strong>Карта:</strong> ${row.user.card_number || 'Не указан'}</div>
                                <div><strong>Телефон СБП:</strong> ${row.user.sbp_phone_number || 'Не указан'}</div>
                                <div><strong>ФИО:</strong> ${row.user.recipient_name || 'Не указано'}</div>
                            `;
                        }
                    },
                    {
                        data: 'amount',
                        render: function (data) {
                            return parseInt(data).toLocaleString('ru-RU') + ' ₽';
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            if (row.is_finished === 0) {
                                return `<span class="elapsed-time"
                                    data-created="${row.created_at}"
                                    data-updated="${row.updated_at}"
                                    data-is-finished="${row.is_finished}">
                                    ${getElapsedTime(row.created_at)}
                                </span>`;
                            }
                            return getElapsedTime(row.created_at, row.updated_at);
                        }
                    },
                    {
                        data: 'is_finished',
                        render: function (data, type, row) {
                            if (data === 2) {
                                return '<div class="btn btn-danger">Отмена</div>';
                            } else if (data === 1) {
                                return '<div class="btn btn-success">Выплачено</div>';
                            } else if (data === 0) {
                                return `<div style="display: flex; flex-direction: column; gap: 5px;">
                                    <button class="btn btn-success payout-button" data-id="${row.id}">Выплата</button>
                                    <button class="btn btn-danger cancel-button" data-id="${row.id}">Отмена</button>
                                </div>`;
                            }
                            return 'Неизвестный статус';
                        }
                    }
                ],
                rowCallback: function (row, data) {
                    startTimers();
                },
                processing: true,
                serverSide: false,
                paging: true,
                searching: true,
                ordering: true,
                order: [[0, 'desc']],
                language: {
                    emptyTable: 'Нет активных инвестиций',
                    loadingRecords: 'Загрузка...',
                    lengthMenu: "Показать _MENU_ записей",
                    search: "Поиск:",
                    paginate: {
                        first: "Первая",
                        last: "Последняя",
                        next: "Следующая",
                        previous: "Предыдущая"
                    }
                }
            });

            $('#user-investments').removeClass('d-none');
        } else if (activeButton === 'user_receipts') {
            // Инициализация таблицы чеков
            $('#receipts_table').DataTable({
                ajax: {
                    url: '/api/telegram-users/' + $('#user_id').val() + '/receipts',
                    type: 'GET',
                    dataSrc: function(json) {
                        return json.map(item => ({
                            ...item,
                            amount: item.amount || 0,
                            investment: item.investment || { id: null, amount: 0 }
                        }));
                    }
                },
                columns: [
                    {
                        data: 'created_at',
                        render: function(data) {
                            return data ? new Date(data).toLocaleString('ru-RU') : '-';
                        }
                    },
                    {
                        data: 'amount',
                        render: function(data) {
                            const amount = parseInt(data) || 0;
                            return amount.toLocaleString('ru-RU') + ' ₽';
                        }
                    },
                    {
                        data: 'is_accepted',
                        render: function(data, type, row) {
                            let status = '';
                            if (data === 1) {
                                status = '<span class="badge bg-success">Принят</span>';
                            } else if (data === 2) {
                                status = '<span class="badge bg-danger">Отклонен</span>';
                            } else {
                                status = `
                                    <div class="button-group">
                                        <button class="btn btn-success btn-sm accept-btn" data-id="${row.id}">Принять</button>
                                        <button class="btn btn-info btn-sm clarify-btn" data-id="${row.id}">Уточнить</button>
                                        <button class="btn btn-danger btn-sm reject-btn" data-id="${row.id}">Отклонить</button>
                                    </div>
                                `;
                            }
                            return status;
                        }
                    },
                    {
                        data: 'receipt_path',
                        render: function(data) {
                            if (data) {
                                return '<span class="badge bg-info">Есть чек</span>';
                            }
                            return '<span class="badge bg-secondary">Нет чека</span>';
                        }
                    },
                    {
                        data: 'investment',
                        render: function(data) {
                            const amount = parseInt(data?.amount) || 0;
                            const id = data?.id || 'Н/Д';
                            return `ID: ${id}<br>Сумма: ${amount.toLocaleString('ru-RU')} ₽`;
                        }
                    }
                ],
                order: [[0, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json'
                }
            });

            // Обработчик для кнопки "Принять"
            $('#receipts_table tbody').on('click', '.accept-btn', function(e) {
                e.stopPropagation();
                const receiptId = $(this).data('id');
                $.ajax({
                    url: `/api/receipts/${receiptId}/accept`,
                    type: 'POST',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Ошибка', xhr.responseJSON?.message || 'Произошла ошибка', 'error');
                    }
                });
            });

            // Обработчик для кнопки "Отклонить"
            $('#receipts_table tbody').on('click', '.reject-btn', function(e) {
                e.stopPropagation();
                const receiptId = $(this).data('id');

                Swal.fire({
                    title: 'Подтверждение',
                    text: 'Вы действительно хотите отклонить этот чек?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Да, отклонить',
                    cancelButtonText: 'Отмена'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/receipts/${receiptId}/reject`,
                            type: 'POST',
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Ошибка', xhr.responseJSON?.message || 'Произошла ошибка', 'error');
                            }
                        });
                    }
                });
            });

            // Обработчик для кнопки "Уточнить"
            $('#receipts_table tbody').on('click', '.clarify-btn', function(e) {
                e.stopPropagation();
                const receiptId = $(this).data('id');
                $.ajax({
                    url: `/api/receipts/${receiptId}/clarify`,
                    type: 'POST',
                    success: function(response) {
                        Swal.fire('Успешно', 'Уведомление отправлено пользователю', 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Ошибка', xhr.responseJSON?.message || 'Произошла ошибка', 'error');
                    }
                });
            });

            // Обработчик клика по строке для просмотра чека
            $('#receipts_table tbody').on('click', 'tr', function() {
                const data = $('#receipts_table').DataTable().row(this).data();

                if (data && data.receipt_path) {
                    const imageUrl = '/storage/' + data.receipt_path;

                    Swal.fire({
                        title: 'Чек',
                        imageUrl: imageUrl,
                        imageWidth: '100%',
                        imageHeight: 'auto',
                        imageAlt: 'Чек',
                        showCloseButton: true,
                        showConfirmButton: false,
                        width: '800px',
                        customClass: {
                            image: 'img-fluid',
                            popup: 'swal-wide'
                        }
                    });
                }
            });

            $('#user-receipts-content').removeClass('d-none');
        }

    });

    let currentAction = null;  // Переменная для хранения текущего действия (выплата или отмена)
    let currentInvestmentId = null;  // Переменная для хранения ID текущей инвестиции

    // Обработчик для кнопки "Выплата"
    $(document).on('click', '.payout-button', function () {
        const investmentId = $(this).data('id');

        Swal.fire({
            title: 'Подтверждение',
            text: 'Вы уверены, что хотите выплатить эту инвестицию?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, выплатить',
            cancelButtonText: 'Отмена'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Выполнение выплаты...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/api/investments/${investmentId}/payout`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Инвестиция успешно выплачена',
                            icon: 'success'
                        }).then(() => {
                            $('#investments_table').DataTable().ajax.reload();
                        });
                    },
                    error: function (response) {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Ошибка при выплате: ' + response.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Обработчик для кнопки "Отмена"
    $(document).on('click', '.cancel-button', function () {
        const investmentId = $(this).data('id');

        Swal.fire({
            title: 'Подтверждение',
            text: 'Вы уверены, что хотите отменить эту инвестицию?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, отменить',
            cancelButtonText: 'Нет'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Отмена инвестиции...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/api/investments/${investmentId}/cancel`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Инвестиция успешно отменена',
                            icon: 'success'
                        }).then(() => {
                            $('#investments_table').DataTable().ajax.reload();
                        });
                    },
                    error: function (response) {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Ошибка при отмене: ' + response.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });


    // Обработчик для кнопки добавления Instagram
    $(document).on('click', '[data-bs-target="#instagramModal"]', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Добавить/Изменить Инстаграм',
            input: 'text',
            inputLabel: 'Ник инстаграм',
            inputValue: $('#instagramUsername').val(),
            showCancelButton: true,
            confirmButtonText: 'Сохранить',
            cancelButtonText: 'Отмена',
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Введите ник инстаграм');
                    return false;
                }
                return { instagram: value };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/telegram-users/update-instagram',
                    type: 'POST',
                    data: {
                        telegram_user_id: $('#user_id').val(),
                        instagram: result.value.instagram,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Instagram успешно обновлен',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function (response) {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Ошибка при обновлении Instagram: ' + response.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Обработчик для кнопки добавления реквизитов
    $(document).on('click', '[data-bs-target="#addPropsModal"]', function (e) {
        e.preventDefault();

        // Получаем значения из информации о пользователе, очищая от меток
        const bankText = $('.card-body').text().match(/Банк: (.*?)(?=\n|$)/)?.[1]?.trim() || '';
        const cardText = $('.card-body').text().match(/Номер карты: (.*?)(?=\n|$)/)?.[1]?.trim() || '';
        const sbpText = $('.card-body').text().match(/Номер телефона СПБ: (.*?)(?=\n|$)/)?.[1]?.trim() || '';
        const nameText = $('.card-body').text().match(/ФИО: (.*?)(?=\n|$)/)?.[1]?.trim() || '';

        // Убираем "Не указан" если такое значение есть
        const bank = bankText === "Не указан" ? "" : bankText;
        const card = cardText === "Не указан" ? "" : cardText;
        const sbp = sbpText === "Не указан" ? "" : sbpText;
        const name = nameText === "Не указан" ? "" : nameText;


        Swal.fire({
            title: 'Добавить/Изменить реквизиты',
            html: `
                <div class="form-group">
                    <label class="swal2-label">Название банка</label>
                    <input id="swal-bank" class="swal2-input" value="${bank}" placeholder="Введите название банка">
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">Номер карты</label>
                    <input id="swal-card" class="swal2-input" value="${card}" placeholder="Введите номер карты" maxlength="19">
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">Номер телефона СБП</label>
                    <input id="swal-sbp" class="swal2-input" value="${sbp}" placeholder="Введите номер телефона">
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">ФИО получателя</label>
                    <input id="swal-name" class="swal2-input" value="${name}" placeholder="Введите ФИО">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Сохранить',
            cancelButtonText: 'Отмена',
            didOpen: () => {
                // Добавляем форматирование номера карты
                const cardInput = document.getElementById('swal-card');
                cardInput.addEventListener('input', function (e) {
                    let value = this.value.replace(/\D/g, '');
                    let formattedValue = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i > 0 && i % 4 === 0) {
                            formattedValue += ' ';
                        }
                        formattedValue += value[i];
                    }
                    this.value = formattedValue.substring(0, 19);
                });
            },
            preConfirm: () => {
                const bank = document.getElementById('swal-bank').value.trim();
                const card = document.getElementById('swal-card').value.trim();
                const sbp = document.getElementById('swal-sbp').value.trim();
                const name = document.getElementById('swal-name').value.trim();

                // Возвращаем только непустые значения
                const data = {};
                if (bank) data.bank = bank;
                if (card) data.card_number = card;
                if (sbp) data.sbp_phone_number = sbp;
                if (name) data.recipient_name = name;

                return data;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/telegram-users/update-props',
                    type: 'POST',
                    headers: {
                        'X-Admin-Token': ADMIN_TOKEN
                    },
                    data: {
                        user_id: $('#telegram_user_id').text(),
                        ...result.value,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Реквизиты успешно обновлены',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function (response) {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Ошибка при обновлении реквизитов: ' + response.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Обработчик для кнопки добавления инвестиции
    $(document).on('click', '[data-bs-target="#addInvestitionModal"]', function (e) {
        e.preventDefault();

        // Получаем значения из информации о пользователе
        const bankText = $('.card-body').text().match(/Банк: (.*?)(?=\n|$)/)?.[1]?.trim() || '';
        const cardText = $('.card-body').text().match(/Номер карты: (.*?)(?=\n|$)/)?.[1]?.trim() || '';
        const sbpText = $('.card-body').text().match(/Номер телефона СПБ: (.*?)(?=\n|$)/)?.[1]?.trim() || '';
        const nameText = $('.card-body').text().match(/ФИО: (.*?)(?=\n|$)/)?.[1]?.trim() || '';

        // Убираем "Не указан" если такое значение есть
        const bank = bankText === "Не указан" ? "" : bankText;
        const card = cardText === "Не указан" ? "" : cardText;
        const sbp = sbpText === "Не указан" ? "" : sbpText;
        const name = nameText === "Не указан" ? "" : nameText;

        Swal.fire({
            title: 'Добавить инвестицию',
            html: `
                <div class="form-group">
                    <label class="swal2-label">Название банка</label>
                    <input id="swal-bank-invest" class="swal2-input" value="${bank}" placeholder="Введите название банка">
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">Номер карты</label>
                    <input id="swal-card-invest" class="swal2-input" value="${card}" placeholder="Введите номер карты" maxlength="19">
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">Номер телефона СБП</label>
                    <input id="swal-sbp-invest" class="swal2-input" value="${sbp}" placeholder="Введите номер телефона">
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">ФИО получателя</label>
                    <input id="swal-name-invest" class="swal2-input" value="${name}" placeholder="Введите ФИО">
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">Валюта</label>
                    <select id="swal-currency" class="swal2-select">
                        <option value="RUB">RUB</option>
                        <option value="TRX">TRX</option>
                    </select>
                </div>
                <br>
                <div class="form-group">
                    <label class="swal2-label">Сумма инвестиции</label>
                    <input id="swal-amount" class="swal2-input" type="text" placeholder="Введите сумму">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Далее',
            cancelButtonText: 'Отмена',
            didOpen: () => {
                // Форматирование суммы
                const amountInput = document.getElementById('swal-amount');
                amountInput.addEventListener('input', function (e) {
                    let value = this.value.replace(/\D/g, '');
                    if (value) {
                        value = Number(value).toLocaleString('ru-RU');
                    }
                    this.value = value;
                });

                // Форматирование номера карты
                const cardInput = document.getElementById('swal-card-invest');
                cardInput.addEventListener('input', function (e) {
                    // Убираем все нецифровые символы
                    let value = this.value.replace(/\D/g, '');

                    // Добавляем пробелы после каждых 4 цифр
                    let formattedValue = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i > 0 && i % 4 === 0) {
                            formattedValue += ' ';
                        }
                        formattedValue += value[i];
                    }

                    // Обрезаем до 19 символов (16 цифр + 3 пробела)
                    this.value = formattedValue.substring(0, 19);
                });
            },
            preConfirm: () => {
                return {
                    bank: document.getElementById('swal-bank-invest').value,
                    card_number: document.getElementById('swal-card-invest').value,
                    sbp_phone_number: document.getElementById('swal-sbp-invest').value,
                    recipient_name: document.getElementById('swal-name-invest').value,
                    currency: document.getElementById('swal-currency').value,
                    amount: document.getElementById('swal-amount').value.replace(/\s/g, '')
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Форматируем сумму для отображения
                const formattedAmount = parseInt(result.value.amount).toLocaleString('ru-RU');
                const currencySymbol = result.value.currency;

                // Показываем подтверждение
                Swal.fire({
                    title: 'Подтверждение',
                    text: `Вы уверены, что хотите добавить инвестицию на сумму ${formattedAmount} ${currencySymbol}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Да, добавить',
                    cancelButtonText: 'Отмена',
                    allowOutsideClick: false
                }).then((confirmResult) => {
                    if (confirmResult.isConfirmed) {
                        // Показываем индикатор загрузки
                        Swal.fire({
                            title: 'Добавление инвестиции...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Отправляем AJAX запрос
                        $.ajax({
                            url: '/api/investments',
                            type: 'POST',
                            headers: {
                                'X-Admin-Token': ADMIN_TOKEN
                            },
                            data: {
                                telegram_user_id: $('#user_id').val(),
                                ...result.value,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Успешно!',
                                    text: 'Инвестиция успешно добавлена',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function (response) {
                                Swal.fire({
                                    title: 'Ошибка!',
                                    text: 'Ошибка при добавлении инвестиции: ' + response.responseJSON.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }
        });
    });

});

function startTimers() {
    setInterval(function () {
        // Ищем все элементы с классом elapsed-time дл обновления таймера
        $('.elapsed-time').each(function () {
            const created_at = $(this).data('created');
            const updated_at = $(this).data('updated');
            const isFinished = $(this).data('is-finished');

            // Обновляем таймер только для тех инвестиций, которые не завершены (is_finished === 0)
            if (isFinished === 0) {
                const timeDisplay = getElapsedTime(created_at); // Рассчитываем время для активных инвестиций
                $(this).html(timeDisplay);  // Обновляем текс таймера
            }
        });
    }, 1000);  // Обновление каждую секунду
}

function getElapsedTime(created_at, updated_at = null) {
    const createdAtDate = new Date(created_at);
    const endTime = updated_at ? new Date(updated_at).getTime() : new Date().getTime(); // Используем updated_at, если оно есть, иначе текущее время
    const elapsedTime = endTime - createdAtDate.getTime();

    const hours = Math.floor(elapsedTime / (1000 * 60 * 60));
    const minutes = String(Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
    const seconds = String(Math.floor((elapsedTime % (1000 * 60)) / 1000)).padStart(2, '0');

    const highlightLimit = parseInt($('#highlightHours').val(), 10);  // Часы для подсветки

    // Подсвечиваем, если прошло больше указанного времени
    if (hours >= highlightLimit) {
        return `<span style="color: red;">${hours}:${minutes}:${seconds}</span>`;
    } else {
        return `${String(hours).padStart(2, '0')}:${minutes}:${seconds}`;
    }
}
