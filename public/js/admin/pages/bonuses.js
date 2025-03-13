$(document).ready(function () {

    $('#user-investments').DataTable({
        ajax: {
            url: '/api/bonus-payouts',
            type: 'POST',
            contentType: 'application/json',
            dataSrc: function (json) {
                if (json.length === 0) {
                    return [];
                }
                console.log(json);
                return json;
            },
        },
        columns: [
            {
                data: 'telegram_user_id',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<a href="/admin/users/${row.id}" class="user-link">${data}</a>`;
                    }
                    return data;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <div><strong>Банк:</strong> ${row.bank || 'Не указан'}</div>
                        <div><strong>Карта:</strong> ${row.card_number || 'Не указан'}</div>
                        <div><strong>Телефон СБП:</strong> ${row.sbp_phone_number || 'Не указан'}</div>
                        <div><strong>ФИО:</strong> ${row.recipient_name || 'Не указано'}</div>
                    `;
                }
            },
            {
                data: 'referral_balance',
                render: function (data, type, row) {
                    if (type === 'sort' || type === 'filter') {
                        return data;
                    }
                    return parseInt(data).toLocaleString('ru-RU', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0,
                        useGrouping: true
                    });
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<div style="display: flex; flex-direction: column; gap: 5px;">
                        <button class="btn btn-success payout-button" data-id="${row.id}" data-amount="${row.referral_balance}">Выплата</button>
                    </div>`;
                }
            }
        ],
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        }
    });

    $('#user-investments-content').removeClass('d-none');

    // Запускаем автоматическое обновление при загрузке страницы
    window.bonusesUpdateInterval = setInterval(updateBonuses, 5000);

    $("input[name='page_selector']").change(function () {
        var activeButton = $("input[name='page_selector']:checked").attr("id");

        // Очищаем все существующие интервалы
        if (window.bonusesUpdateInterval) {
            clearInterval(window.bonusesUpdateInterval);
            window.bonusesUpdateInterval = null;
        }
        if (window.bonusesHistoryUpdateInterval) {
            clearInterval(window.bonusesHistoryUpdateInterval);
            window.bonusesHistoryUpdateInterval = null;
        }

        if ($.fn.DataTable.isDataTable('#user-investments')) {
            $('#user-investments').DataTable().clear().destroy();
        }

        if ($.fn.DataTable.isDataTable('#user-investments-history')) {
            $('#user-investments-history').DataTable().clear().destroy();
        }

        $('#user-history-content, #user-investments-content').addClass('d-none');

        if (activeButton === "investments_wait") {
            $('#user-investments').DataTable({
                ajax: {
                    url: '/api/bonus-payouts',
                    type: 'POST',
                    contentType: 'application/json',
                    dataSrc: function (json) {
                        if (json.length === 0) {
                            return [];
                        }
                        return json;
                    },
                },
                columns: [
                    {
                        data: 'telegram_user_id',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return `<a href="/admin/users/${row.id}" class="user-link">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <div><strong>Банк:</strong> ${row.bank || 'Не указан'}</div>
                                <div><strong>Карта:</strong> ${row.card_number || 'Не указан'}</div>
                                <div><strong>Телефон СБП:</strong> ${row.sbp_phone_number || 'Не указан'}</div>
                                <div><strong>ФИО:</strong> ${row.recipient_name || 'Не указано'}</div>
                            `;
                        }
                    },
                    {
                        data: 'referral_balance',
                        render: function (data, type, row) {
                            if (type === 'sort' || type === 'filter') {
                                return data;
                            }
                            return parseInt(data).toLocaleString('ru-RU', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                                useGrouping: true
                            });
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<div style="display: flex; flex-direction: column; gap: 5px;">
                                <button class="btn btn-success payout-button" data-id="${row.id}" data-amount="${row.referral_balance}">Выплата</button>
                            </div>`;
                        }
                    }
                ],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                }
            });
            $('#user-investments-content').removeClass('d-none');
            // Запускаем обновление активных бонусов
            window.bonusesUpdateInterval = setInterval(updateBonuses, 5000);
        } else if (activeButton === "investments_history") {
            $('#user-investments-history').DataTable({
                ajax: {
                    url: '/api/bonus-payouts/history',
                    type: 'POST',
                    contentType: 'application/json',
                    dataSrc: function (json) {
                        if (json.length === 0) {
                            return [];
                        }
                        return json;
                    },
                },
                columns: [
                    { data: 'telegram_user_id' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <div><strong>Банк:</strong> ${row.bank || 'Не указан'}</div>
                                <div><strong>Карта:</strong> ${row.card_number || 'Не указан'}</div>
                                <div><strong>Телефон СБП:</strong> ${row.sbp_phone_number || 'Не указан'}</div>
                                <div><strong>ФИО:</strong> ${row.recipient_name || 'Не указано'}</div>
                            `;
                        }
                    },
                    {
                        data: 'amount',
                        render: function (data, type, row) {
                            let formattedAmount = parseInt(data).toLocaleString('ru-RU', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            return formattedAmount;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<div style="display: flex; flex-direction: column; gap: 5px;">
                                <button class="btn btn-success  ">Выплачено</button>
                            </div>`;
                        }
                    }
                ]
            });
            $('#user-history-content').removeClass('d-none');
            // Запускаем обновление истории бонусов
            window.bonusesHistoryUpdateInterval = setInterval(updateBonusesHistory, 5000);
        }
    });

    $(document).on('click', '.payout-button', function (e) {
        e.stopPropagation();
        const userId = $(this).data('id');
        const amount = $(this).data('amount');

        // Заполняем данные модального окна
        $('#confirmModal').find('.modal-body').html(`
            Вы уверены, что хотите подтвердить выплату на сумму ${parseInt(amount).toLocaleString('ru-RU')} ₽?
        `);
        $('#confirmModal').find('.btn-confirm').data('id', userId);
        $('#confirmModal').modal('show');
    });

    // Обработчик подтверждения выплаты
    $('#confirmModal').find('.btn-confirm').on('click', function() {
        const userId = $(this).data('id');

        $.ajax({
            url: '/api/bonus-payouts/confirm',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: userId }),
            success: function(response) {
                if (response.success) {
                    toastr.success('Выплата успешно подтверждена');
                    $('#confirmModal').modal('hide');
                    // Обновляем таблицу
                    $('#user-investments').DataTable().ajax.reload();
                } else {
                    toastr.error('Произошла ошибка при подтверждении выплаты');
                }
            },
            error: function() {
                toastr.error('Произошла ошибка при подтверждении выплаты');
            }
        });
    });

    $(document).on('click', '.btn-delete', function () {
        withdrawalId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

})

$(document).on('click', '.payout-button', function () {
    const bonusId = $(this).data('id');
    const defaultAmount = parseInt($(this).data('amount')).toString();

    Swal.fire({
        title: 'Выплата бонусов',
        input: 'number',
        inputLabel: 'Сумма выплаты',
        inputValue: defaultAmount,
        inputAttributes: {
            min: '1',
            required: 'true'
        },
        showCancelButton: true,
        confirmButtonText: 'Далее',
        cancelButtonText: 'Отмена',
        focusConfirm: false,
        preConfirm: (value) => {
            const amount = Number(value);
            if (!amount || amount <= 0) {
                Swal.showValidationMessage('Пожалуйста, введите корректную сумму');
                return false;
            }
            return { amount: amount };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const enteredAmount = result.value.amount;

            Swal.fire({
                title: 'Подтверждение выплаты',
                text: `Вы уверены, что хотите выплатить бонусы на сумму ${enteredAmount}₽?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Да, выплатить',
                cancelButtonText: 'Отмена',
                allowOutsideClick: false
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    Swal.fire({
                        title: 'Выплата бонусов...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/api/bonus-payouts/${bonusId}/payout`,
                        type: 'POST',
                        data: {
                            amount: enteredAmount,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Успешно!',
                                text: 'Бонусы успешно выплачены',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                if ($.fn.DataTable.isDataTable('#user-investments')) {
                                    $('#user-investments').DataTable().ajax.reload();
                                }
                                if ($.fn.DataTable.isDataTable('#user-investments-history')) {
                                    $('#user-investments-history').DataTable().ajax.reload();
                                }
                            });
                        },
                        error: function (response) {
                            Swal.fire({
                                title: 'Ошибка!',
                                text: response.responseJSON.message,
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

function getElapsedTime(created_at, highlightHours = 48) {
    const createdAtDate = new Date(created_at);
    const now = new Date().getTime();
    const elapsedTime = now - createdAtDate.getTime();

    const hours = Math.floor(elapsedTime / (1000 * 60 * 60));
    const minutes = String(Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
    const seconds = String(Math.floor((elapsedTime % (1000 * 60)) / 1000)).padStart(2, '0');

    const highlightLimit = parseInt($('#highlightHours').val(), 10);  // Часы для подсветки

    if (hours >= highlightLimit) {
        return `<span style="color: red;">${hours}:${minutes}:${seconds}</span>`;
    } else {
        return `${String(hours).padStart(2, '0')}:${minutes}:${seconds}`;
    }
}

function updateBonusesHistory() {
    $.ajax({
        url: '/api/bonus-payouts/history',
        type: 'POST',
        contentType: 'application/json',
        success: function (data) {
            // Фильтруем только выплаченные бонусы
            data = data.filter(item => item.is_finished === 1);

            const table = $('#user-investments-history').DataTable();
            const currentData = table.rows().data().toArray();
            const newData = data.map(item => item.id);

            // Удаляем строки, которых больше нет
            table.rows().every(function () {
                const rowData = this.data();
                if (rowData && !newData.includes(rowData.id)) {
                    this.remove();
                }
            });
            table.draw(false);

            // Добавляем новые строки
            data.forEach(item => {
                if (!currentData.some(row => row.id === item.id)) {
                    table.row.add(item).draw(false);
                }
            });
        }
    });
}

// Добавляем функцию обновления активных бонусов
function updateBonuses() {
    $.ajax({
        url: '/api/bonus-payouts',
        type: 'POST',
        contentType: 'application/json',
        success: function (data) {
            const table = $('#user-investments').DataTable();
            const currentData = table.rows().data().toArray();
            const newData = data.map(item => item.id);

            // Удаляем строки, которых больше нет
            table.rows().every(function () {
                const rowData = this.data();
                if (!newData.includes(rowData.id)) {
                    this.remove();
                }
            });
            table.draw(false);

            // Добавляем новые строки
            data.forEach(item => {
                if (!currentData.some(row => row.id === item.id)) {
                    table.row.add(item).draw(false);
                }
            });
        }
    });
}

// Очищаем интервалы при уничтожении страницы
$(window).on('unload', function () {
    if (window.bonusesUpdateInterval) {
        clearInterval(window.bonusesUpdateInterval);
    }
    if (window.bonusesHistoryUpdateInterval) {
        clearInterval(window.bonusesHistoryUpdateInterval);
    }
});
