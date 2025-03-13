$(document).ready(function() {
    const table = $('#receipts-table').DataTable({
        ajax: {
            url: '/api/receipts_table',
            type: 'GET',
            dataSrc: function(json) {
                // Преобразуем объект в массив
                return Object.values(json.data);
            }
        },
        columns: [
            {
                data: 'sender',
                render: function(data) {
                    console.log(data); // Выводим все полученные данные в консоль
                    return `ID: ${data.telegram_user_id}`;
                }
            },
            {
                data: 'receiver',
                render: function(data) {
                    console.log(data); // Выводим все полученные данные в консоль
                    return `ID: ${data.telegram_user_id}`;
                }
            }
        ],
        order: [[1, 'desc']],
        language: {
            url: '/js/ru.json'
        },
        initComplete: function() {
            // Automatically open the first row
            const firstRow = $('#receipts-table tbody tr').first();
            const row = table.row(firstRow);

            if (!row.child.isShown()) {
                row.child(format(row.data())).show();
                firstRow.addClass('shown');
                // Focus on the "Accept" button
                firstRow.find('.accept-btn').focus();
            }
        }
    });

    // Добавляем новую таблицу для истории чеков
    const historyTable = $('#receipts-history-table').DataTable({
        ajax: {
            url: '/api/receipts_history',
            type: 'GET',
            dataSrc: function(json) {
                console.log(json.data);
                return json.data;
            }
        },
        responsive: {
            details: false
        },
        columnDefs: [
            {
                targets: [2, 3, 4],
                className: 'none'
            }
        ],
        columns: [
            {
                data: 'sender',
                className: 'all',
                render: function(data) {
                    return `<a href="/admin/users/${data.id}">${data.telegram_user_id}</a>`;
                }
            },
            {
                data: 'receiver',
                className: 'all',
                render: function(data) {
                    return `<a href="/admin/users/${data.id}">${data.telegram_user_id}</a>`;
                }
            },
            {
                data: 'amount',
                render: data => `${data} ₽`
            },
            {
                data: 'details_added_at',
                render: function(data, type, row) {
                    if (type === 'sort') {
                        return data ? new Date(data).getTime() : 0;
                    }
                    return data ? new Date(data).toLocaleString('ru-RU') : 'Неизвестно';
                }
            },
            {
                data: 'is_accepted',
                render: function(data) {
                    if (data === 1) {
                        return '<button class="btn btn-success">Принято</button>';
                    } else if (data === 2) {
                        return '<button class="btn btn-danger">Отклонено</button>';
                    } else {
                        return '<button class="btn btn-secondary">Неизвестно</button>';
                    }
                }
            }
        ],
        order: [[3, 'desc']], // Сортировка по дате создания по убыванию
        language: {
            url: '/js/ru.json'
        }
    });

    // Add event listener for opening and closing details in history table
    $('#receipts-history-table tbody').on('click', 'tr', function() {
        const tr = $(this);
        const row = historyTable.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(formatHistory(row.data())).show();
            tr.addClass('shown');
        }
    });

    // Formatting function for row details in history table
    function formatHistory(data) {
        console.log(data)
        let receiptImage = '';
        if (data && data.receipt_path) {
            receiptImage = `
                <div class="col-md-6">
                    <img src="/storage/${data.receipt_path}" alt="Чек" class="img-fluid" style="height: 700px;">
                </div>
            `;
        }

        const updatedAt = data && data.details_added_at ? new Date(data.details_added_at).toLocaleString('ru-RU') : 'Неизвестно';
        const bank = data && data.receiver && data.receiver.bank ? data.receiver.bank : 'Неизвестно';
        const recipientName = data && data.receiver && data.receiver.recipient_name ? data.receiver.recipient_name : 'Неизвестно';
        const cardNumber = data && data.receiver && data.receiver.card_number ? data.receiver.card_number : 'Неизвестно';
        const amount = data && data.amount ? `${data.amount} ₽` : 'Неизвестно';
        const receiptId = data && data.receipt_id ? data.receipt_id : 'Неизвестно';

        return `
            <div class="row">
                ${receiptImage}
                <div class="col-md-6">
                    <div class="details-container">
                        <p><strong>Дата создания:</strong> ${updatedAt}</p>
                        <p><strong>Банк:</strong> ${bank}</p>
                        <p><strong>ФИО:</strong> ${recipientName}</p>
                        <p><strong>Карта:</strong> ${cardNumber}</p>
                        <p><strong>Сумма:</strong> ${amount}</p>
                    </div>
                </div>
            </div>
        `;
    }

    // Переключение вкладок
    $("input[name='page_selector']").change(function () {
        const activeButton = $("input[name='page_selector']:checked").attr("id");

        $('#receipts-table-content, #receipts-history-content').addClass('d-none');

        if (activeButton === 'receipts_table') {
            $('#receipts-table-content').removeClass('d-none');
        } else if (activeButton === 'receipts_history') {
            $('#receipts-history-content').removeClass('d-none');
            historyTable.ajax.reload();
        }
    });

    // Add event listener for opening and closing details
    $('#receipts-table tbody').on('click', 'tr', function() {
        const tr = $(this);
        const row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
            // Focus on the "Accept" button
            tr.find('.accept-btn').focus();
        }
    });

    // Formatting function for row details
    function format(data) {
        let receiptImage = '';
        if (data && data.receipt_path) {
            receiptImage = `
                <div class="col-md-6">
                    <img src="/storage/${data.receipt_path}" alt="Чек" class="img-fluid" style="height: 700px;">
                </div>
            `;
        }

        const updatedAt = data && data.details_added_at ? new Date(data.details_added_at).toLocaleString('ru-RU') : 'Неизвестно';
        const bank = data && data.receiver && data.receiver.bank ? data.receiver.bank : 'Неизвестно';
        const recipientName = data && data.receiver && data.receiver.recipient_name ? data.receiver.recipient_name : 'Неизвестно';
        const cardNumber = data && data.receiver && data.receiver.card_number ? data.receiver.card_number : 'Неизвестно';
        const amount = data && data.amount ? `${data.amount} ₽` : 'Неизвестно';
        const receiptId = data && data.receipt_id ? data.receipt_id : 'Неизвестно';

        return `
            <div class="row">
                ${receiptImage}
                <div class="col-md-6">
                    <div class="details-container">
                        <p><strong>Дата создания:</strong> ${updatedAt}</p>
                        <p><strong>Банк:</strong> ${bank}</p>
                        <p><strong>ФИО:</strong> ${recipientName}</p>
                        <p><strong>Карта:</strong> ${cardNumber}</p>
                        <p><strong>Сумма:</strong> ${amount}</p>
                        <div class="button-group">
                            <button class="btn btn-success accept-btn" data-id="${receiptId}">Принять</button>
                            <button class="btn btn-danger reject-btn" data-id="${receiptId}">Отклонить</button>
                            <button class="btn btn-info clarify-btn" data-id="${receiptId}">Уточнить</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Event listeners for buttons
    $('#receipts-table tbody').on('click', '.accept-btn', function(e) {
        e.stopPropagation(); // Prevent row click event
        const receiptId = $(this).data('id');
        // Handle accept action
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

    $('#receipts-table tbody').on('click', '.reject-btn', function(e) {
        e.stopPropagation(); // Prevent row click event
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
    $('#receipts-table tbody').on('click', '.clarify-btn', function(e) {
        e.stopPropagation(); // Prevent row click event
        const receiptId = $(this).data('id');
        // Отправляем запрос на сервер для отправки уведомления
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
});
