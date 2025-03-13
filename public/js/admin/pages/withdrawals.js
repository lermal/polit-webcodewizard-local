function formatDetails(data) {
    let details = '<div class="child-row-details collapsed">';

    // Получаем адрес из транзакции, если есть txid
    let addressInfo = '';
    if (data.txid) {
        // Добавляем ссылку на tronscan
        addressInfo = `
            <p><strong>TxID:</strong> <a href="https://tronscan.org/#/transaction/${data.txid}" target="_blank">${data.txid}</a></p>
            <p><strong>Адрес отправителя:</strong> ${data.trx_transaction?.from || 'Не найден'}</p>
        `;
    }

    details += `
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID ТГ:</strong> ${data.user.telegram_user_id || 'Не указано'}</p>
                <p><strong>Банк:</strong> ${data.user.bank || 'Не указано'}</p>
                <p><strong>Карта:</strong> ${data.user.card_number ? data.user.card_number.replace(/(\d{4})(?=\d)/g, '$1 ') : 'Не указана'}</p>
                <p><strong>Дата создания:</strong> ${new Date(data.created_at).toLocaleString('ru-RU')}</p>
                ${addressInfo}
            </div>
            <div class="col-md-6">
                <p><strong>Телефон:</strong> ${data.user.sbp_phone_number || 'Не указано'}</p>
                <p><strong>ФИО:</strong> ${data.user.recipient_name || 'Не указано'}</p>
                <p><strong>Сумма инвестиции:</strong> ${parseInt(data.amount).toLocaleString('ru-RU')} ₽</p>
                <p><strong>Сумма выплаты:</strong> ${(parseInt(data.amount) + (parseInt(data.amount) * parseFloat(data.percent) / 100)).toLocaleString('ru-RU')} ₽</p>
            </div>
        </div>
    `;

    if (data.user_statuses && data.user_statuses.length > 0) {
        details += '<div class="row mt-3"><div class="col-12"><strong>Статусы пользователей:</strong><ul>';
        data.user_statuses.forEach(status => {
            let hasPendingStatus = status.status === 'Ожидается оплата' ? true : false;
            let acceptButton = hasPendingStatus ? `<br><a href="#" class="accept-receipt-button" style="color: #198754 !important" data-id="${status.receipt_id}"><small>Принять</small></a>` : '';
            let rejectButton = hasPendingStatus ? `/<a href="#" class="reject-receipt-button" style="color: #dc3545 !important" data-id="${status.receipt_id}"><small>Отклонить</small></a>` : '';
            details += `<li>ID: ${status.id}, Telegram ID: <a href="/admin/users/${status.id}" target="_blank">${status.telegram_user_id}</a>, Статус: ${status.status} ${acceptButton}${rejectButton}</li>`;
        });
        details += '</ul></div></div>';
    }

    details += `
        ${data.is_finished === 0 ? `
            <div class="row mt-3">
                <div class="col-12">
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button class="btn btn-success payout-button" data-id="${data.id}">Выплата</button>
                        <button class="btn btn-warning partial-payout-button" data-total-amount="${parseInt(data.amount) + (parseInt(data.amount) * parseInt(data.percent) / 100)}" data-partial-payout="${data.partial_payout ?? 0}" data-id="${data.id}">Часть</button>
                        <button class="btn btn-info clarify-button" data-id="${data.id}">Уточнить</button>
                        <button class="btn btn-danger cancel-button" data-id="${data.id}">Отмена</button>
                    </div>
                </div>
            </div>
        ` : data.is_finished === 1 ?
            '<div class="btn btn-success">Выплачено</div>' :
            '<div class="btn btn-danger">Отмена</div>'
        }
    `;
    details += '</div>';
    return details;
}

// Добавьте функцию проверки мобильного устройства
function isMobileDevice() {
    return window.innerWidth <= 1600; // Стандартная точка перелома для мобильных устройств
}

$(document).ready(function () {
    let success_modal = new bootstrap.Modal(document.getElementById('success-alert-modal'), { keyboard: false });
    let error_modal = new bootstrap.Modal(document.getElementById('danger-alert-modal'), { keyboard: false });

    let totalAmount = 0;
    let totalCount = 0;

    let searchTimer;  // Таймер для задержки поиска

    // Добавьте глобальный обработчик кликов по строкам таблицы
    $(document).on('click', '#user-investments tbody tr', function(event) {
        // Если это не мобильное устройство - игнорируем клик по строке
        if (!isMobileDevice()) {
            return;
        }

        if (!$(event.target).is('button, a')) {
            const table = $('#user-investments').DataTable();
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
                table.rows().every(function() {
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

    function bindSearchHandlers() {
        const searchInput = $('.dataTables_filter input');
        searchInput.off('input keyup change');

        searchInput.on('input keyup change', function(e) {
            clearTimeout(searchTimer);
            const value = this.value;
            searchTimer = setTimeout(function() {
                if (value === searchInput.val() && table) { // Проверяем существование table
                    table.search(value).draw();
                }
            }, 500);
        });

        searchInput.on('blur', function() {
            clearTimeout(searchTimer);
            if (table) { // Проверяем существование table
                table.search(this.value).draw();
            }
        });
    }

    function loadInvestments() {
        const filterAmount = parseInt($('#filterAmount').val(), 10) || 0;
        const highlightLimit = parseInt($('#highlightHours').val(), 10);

        // Инициализируем table
        table = $('#user-investments').DataTable({
            ajax: {
                url: '/api/investments/active',
                type: 'POST',
                contentType: 'application/json',
                dataSrc: function (json) {
                    // Предварительно фильтруем данные
                    const currentTime = new Date().getTime();
                    const filteredData = json.filter(row => {
                        const profit = parseFloat(row.amount) * (parseFloat(row.percent) / 100);
                        const sum = parseFloat(row.amount) + profit;
                        return filterAmount === 0 || sum === filterAmount;
                    });

                    // Вычисляем общую сумму только один раз
                    const totals = filteredData.reduce((acc, row) => {
                        const elapsedHours = (currentTime - new Date(row.created_at).getTime()) / (1000 * 60 * 60);

                        if (elapsedHours >= highlightLimit) {
                            const profit = parseFloat(row.amount) * (parseFloat(row.percent) / 100);
                            if (row.currency === 'TRX') {
                                acc.totalTrx = (acc.totalTrx || 0) + parseFloat(row.amount) + profit;
                            } else {
                                acc.totalRub = (acc.totalRub || 0) + parseFloat(row.amount) + profit;
                            }
                            acc.totalCount += 1;
                        }
                        return acc;
                    }, { totalRub: 0, totalTrx: 0, totalCount: 0 });

                    // Обновляем отображение итогов с учетом разных валют
                    let totalAmountText = '';
                    if (totals.totalRub > 0) {
                        totalAmountText += totals.totalRub.toLocaleString('ru-RU') + ' RUB';
                    }
                    if (totals.totalTrx > 0) {
                        if (totalAmountText) totalAmountText += ' / ';
                        totalAmountText += totals.totalTrx.toLocaleString('ru-RU') + ' TRX';
                    }
                    $('#totalPayoutAmount').text(totalAmountText);
                    $('#totalPayoutCount').text(totals.totalCount);


                    return filteredData;
                }
            },
            processing: true,
            serverSide: false,
            order: [[2, 'asc']],
            pageLength: 100,
            deferRender: true,
            lengthChange: true,
            dom: '<"d-flex justify-content-end align-items-center mb-3"<"me-2"l>f>rtip',
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
            columns: [
                {
                    data: 'user.telegram_user_id',
                    render: function (data, type, row) {
                        // Создаем ссылку на страницу пользователя по ID
                        return `<a href="/admin/users/${row.user.id}" target="_blank">${data}</a>`;
                    }
                },
                {
                    data: null,  // Прошедшее время с момента создания или время меду созданием и завершением
                    render: function (data, type, row) {
                        if (row.is_finished === 1 || row.is_finished === 2) {
                            // Для завершенных или отмененных инвестиций считаем разницу между updated_at и created_at
                            return getElapsedTime(row.created_at, row.updated_at);
                        } else {
                            // Для активных инвестиций считаем с момента создания
                            return `
                            <span class="elapsed-time" data-created="${row.created_at}" data-updated="${row.updated_at}" data-is-finished="${row.is_finished}">
                                ${getElapsedTime(row.created_at)}
                            </span>`;
                        }
                    }
                },
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
                    data: 'updated_at',  // Используем оле updated_at для даты вывода
                    render: function (data, type, row) {
                        if (data) {
                            let date = new Date(data);
                            return date.toLocaleString('ru-RU', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                            });
                        }
                        return 'Не указана';  // Если дата не указана
                    },
                    visible: false  // Скрываем толбец по умолчанию
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        // Проверяем существование row и его свойств
                        if (!row || !row.user) {
                            return '';
                        }

                        // Для поиска возвращаем все данные
                        if (type === 'filter' || type === 'sort') {
                            return [
                                row.user.bank || '',
                                (row.user.card_number || '').replace(/\s+/g, ''),
                                row.user.sbp_phone_number || '',
                                row.user.recipient_name || '',
                                row.txid || '',
                                row.trx_transaction?.from || ''
                            ].join(' ');
                        }

                        // Если есть txid, показываем только информацию о транзакции
                        if (row.txid) {
                            return `
                                <div><strong>TxID:</strong> <a href="https://tronscan.org/#/transaction/${row.txid}" target="_blank">${row.txid}</a></div>
                                <div><strong>Адрес отправителя:</strong> ${row.trx_transaction?.from || 'Не найден'}</div>
                            `;
                        }

                        // Проверяем наличие данных пользователя
                        const formattedCard = row.user.card_number ?
                            row.user.card_number.replace(/(\d{4})(?=\d)/g, '$1 ') :
                            'Не указан';

                        return `
                            <div><strong>Банк:</strong> <span style="color: red">${row.user.bank || 'Не указан'}</span></div>
                            <div><strong>Карта:</strong> ${formattedCard}</div>
                            <div><strong>Телефон:</strong> ${row.user.sbp_phone_number || 'Не указан'}</div>
                            <div><strong>ФИО:</strong> ${row.user.recipient_name || 'Не указано'}</div>
                        `;
                    },
                    defaultContent: '' // Значение по умолчанию, если данные отсутствуют
                },
                {
                    data: 'amount',
                    render: function (data, type, row) {
                        if (!row) return '';
                        const currency = row.currency === 'RUB' ? '₽' : row.currency || 'TRX';
                        return parseInt(data).toLocaleString('ru-RU') + ' ' + currency;
                    }
                },
                // Сумма инвестиции и выплаты
                {
                    data: null,  // Ожидаемый доход
                    render: function (data, type, row) {
                        if (!data) return '';

                        const currency = data.currency === 'RUB' ? '₽' : data.currency || 'TRX';
                        let profit = 0;
                        let sum = 0;

                        try {
                            profit = parseFloat(data.amount || 0) * (parseFloat(data.percent || 0) / 100);
                            sum = parseFloat(data.amount || 0) + parseFloat(profit || 0);
                        } catch (e) {
                            console.error('Error calculating profit:', e);
                            return '';
                        }

                        let remaining = sum - parseFloat(data.partial_payout || 0);

                        if (type === 'filter' || type === 'sort') {
                            return parseInt(remaining || 0);
                        }

                        let formattedAmount = '';
                        try {
                            formattedAmount = parseInt(remaining).toLocaleString('ru-RU');
                        } catch (e) {
                            console.error('Error formatting amount:', e);
                            return '';
                        }

                        let userStatusesHtml = '';
                        if (data.user_statuses && data.user_statuses.length > 0) {
                            userStatusesHtml = data.user_statuses.map(status => {
                                if (!status) return '';
                                let hasPendingStatus = status.status === 'Ожидается оплата';
                                let acceptButton = hasPendingStatus ? `<br><a href="#" class="accept-receipt-button" style="color: #198754 !important" data-id="${status.receipt_id}"><small>Принять</small></a>` : '';
                                let rejectButton = hasPendingStatus ? `/<a href="#" class="reject-receipt-button" style="color: #dc3545 !important" data-id="${status.receipt_id}"><small>Отклонить</small></a>` : '';
                                return `<br><small class="text-muted"><a href="/admin/users/${status.id}" target="_blank">${status.telegram_user_id}</a>: ${status.status} (${parseInt(status.amount || 0).toLocaleString('ru-RU')} ${currency})</small>${acceptButton}${rejectButton}`;
                            }).join('');
                        }

                        if (parseFloat(data.partial_payout || 0) > 0) {
                            return `${formattedAmount} ${currency} <br><small class="text-muted">(Выплачено: ${parseInt(data.partial_payout || 0).toLocaleString('ru-RU')} ${currency})</small>` + userStatusesHtml;
                        }

                        return formattedAmount + ' ' + currency + userStatusesHtml;
                    },
                    defaultContent: ''
                },
                {
                    data: 'is_finished',  // Проверка значения is_finished
                    render: function (data, type, row) {
                        if (data === 2) {
                            return '<div class="btn btn-danger">Отмена</div>';  // Если is_finished = 2, выводим "Отмена"
                        } else if (data === 1) {
                            return '<div class="btn btn-success">Выплачено</div>';  // Если is_finished = 1, выводим "Выплачено"
                        } else if (data === 0) {
                            // Если is_finished = 0, выводим кнопки "Выплата" и "Отмена"
                            return `<div style="display: flex; flex-direction: column; gap: 5px;">
                                <button class="btn btn-success payout-button" data-id="${row.id}">Выплата</button>
                                <button class="btn btn-warning partial-payout-button" data-total-amount="${parseInt(row.amount) + (parseInt(row.amount) * parseInt(row.percent) / 100)}" data-partial-payout="${row.partial_payout ?? 0}" data-id="${row.id}">Часть</button>
                                <button class="btn btn-info clarify-button" data-id="${row.id}">Уточнить</button>
                                <button class="btn btn-danger cancel-button" data-id="${row.id}">Отмена</button>
                        </div>`;
                        }
                        return 'Неизвестный статус';  // Если значение не подходит под условия
                    }
                }
            ],
            rowCallback: function(row, data) {
                // Оставляем только startTimers
                startTimers();
            },
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
            },
            // Отключаем стандартный поиск по кнопке
            searching: true,
            search: {
                return: false // Отключаем поиск по Enter
            },
        });

        // Привязываем обработчики после инициализации таблицы
        bindSearchHandlers();

        return table;
    }

    // Загружаем таблицу
    loadInvestments();

    $('#highlightHours').change(function () {
        $('#user-investments').DataTable().clear().destroy();  // Очищаем и уничтожаем таблицу
        loadInvestments();  // Загружаем данные заново с новым значением
    });

    $('#user-investments-content').removeClass('d-none');

    function updateInvestmentsHistory() {
        $.ajax({
            url: '/api/investments/finished_or_cancelled',
            type: 'POST',
            contentType: 'application/json',
            success: function (data) {
                // Фильтруем только завершенные и отмененные инвестиции
                data = data.filter(item => item.is_finished === 1 || item.is_finished === 2);

                const table = $('#user-investments').DataTable();
                const currentData = table.rows().data().toArray();
                const newData = data.map(item => item.id);

                // Удаляем строки, которых больше нет
                table.rows().every(function() {
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


    // Модифицируем обработчик переключения вкладок
    $("input[name='page_selector']").change(function () {
        var activeButton = $("input[name='page_selector']:checked").attr("id");

        // Очищаем все существующие интервалы
        if (window.activeUpdateInterval) {
            clearInterval(window.activeUpdateInterval);
            window.activeUpdateInterval = null;
        }
        if (window.historyUpdateInterval) {
            clearInterval(window.historyUpdateInterval);
            window.historyUpdateInterval = null;
        }

        // Сразу очищаем содержимое таблицы
        if ($.fn.DataTable.isDataTable('#user-investments')) {
            $('#user-investments').DataTable().clear().draw();
            $('#user-investments').DataTable().destroy();
        }

        $('#user-history-content, #user-investments-content').addClass('d-none');

        // Управление отображением и значением фильтра
        const $filterAmount = $('#filterAmount');
        if (activeButton === "investments_wait") {
            $filterAmount.parent().show();
            loadInvestments();
        } else if (activeButton === "investments_history") {
            $filterAmount.val('0');
            $filterAmount.parent().hide();

            // Инициализация таблицы для истории
            table = $('#user-investments').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/api/investments/finished_or_cancelled',
                    type: 'POST'
                },
                order: [[3, 'desc']], // Изменяем сортировку по умолчанию: колонка 3 (Дата вывода) в порядке убывания
                columns: [
                    {
                        data: 'user.telegram_user_id',
                        render: function (data, type, row) {
                            if (!row || !row.user) return '';
                            return `<a href="/admin/users/${row.user.id}" target="_blank">${data}</a>`;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            if (!row) return '';
                            return getElapsedTime(row.created_at, row.updated_at);
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            if (!data) return '';
                            return new Date(data).toLocaleString('ru-RU');
                        }
                    },
                    {
                        data: 'updated_at',
                        render: function(data) {
                            if (!data) return '';
                            return new Date(data).toLocaleString('ru-RU');
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            if (!row || !row.user) return '';

                            // Для поиска
                            if (type === 'filter' || type === 'sort') {
                                return [
                                    row.user.bank || '',
                                    (row.user.card_number || '').replace(/\s+/g, ''),
                                    row.user.sbp_phone_number || '',
                                    row.user.recipient_name || '',
                                    row.txid || '',
                                    row.trx_transaction?.from || ''
                                ].join(' ');
                            }

                            // Если есть txid
                            if (row.txid) {
                                return `
                                    <div><strong>TxID:</strong> <a href="https://tronscan.org/#/transaction/${row.txid}" target="_blank">${row.txid}</a></div>
                                    <div><strong>Адрес отправителя:</strong> ${row.trx_transaction?.from || 'Не найден'}</div>
                                `;
                            }

                            // Банковские реквизиты
                            const formattedCard = row.user.card_number ?
                                row.user.card_number.replace(/(\d{4})(?=\d)/g, '$1 ') :
                                'Не указан';

                            return `
                                <div><strong>Банк:</strong> <span style="color: red">${row.user.bank || 'Не указан'}</span></div>
                                <div><strong>Карта:</strong> ${formattedCard}</div>
                                <div><strong>Телефон:</strong> ${row.user.sbp_phone_number || 'Не указан'}</div>
                                <div><strong>ФИО:</strong> ${row.user.recipient_name || 'Не указано'}</div>
                            `;
                        },
                        defaultContent: ''
                    },
                    {
                        data: 'amount',
                        render: function(data, type, row) {
                            if (!row) return '';
                            const currency = row.currency === 'RUB' ? '₽' : row.currency || 'TRX';
                            return parseInt(data).toLocaleString('ru-RU') + ' ' + currency;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (!row) return '';
                            const currency = row.currency === 'RUB' ? '₽' : row.currency || 'TRX';
                            const profit = parseFloat(row.amount) * (parseFloat(row.percent) / 100);
                            return (parseFloat(row.amount) + profit).toLocaleString('ru-RU') + ' ' + currency;
                        }
                    },
                    {
                        data: 'is_finished',
                        render: function(data) {
                            if (data === undefined) return '';
                            return data === 2 ?
                                '<div class="btn btn-danger">Отмена</div>' :
                                '<div class="btn btn-success">Выплачено</div>';
                        }
                    }
                ],
                pageLength: 100,
                language: {
                    url: '/js/ru.json'
                }
            });
        }

        $('#user-investments-content').removeClass('d-none');
    });

    // Очищаем интервалы при уничтожении страницы
    $(window).on('unload', function() {
        if (window.activeUpdateInterval) {
            clearInterval(window.activeUpdateInterval);
        }
        if (window.historyUpdateInterval) {
            clearInterval(window.historyUpdateInterval);
        }
    });

    $(document).on('click', '.accept-receipt-button', function () {
        const receiptId = $(this).data('id');

        Swal.fire({
            title: 'Подтверждение',
            text: 'Вы уверены, что хотите принять этот чек?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, принять',
            cancelButtonText: 'Отмена'
        }).then((result) => {
            if (result.isConfirmed) {
                // Здесь добавьте AJAX запрос для принятия чека
                $.ajax({
                    url: `/api/receipts/${receiptId}/accept`, // Укажите правильный URL для пинятия чека
                    type: 'POST',
                    success: function (response) {
                        Swal.fire('Успешно!', 'Чек принят.', 'success');
                        $('#user-investments').DataTable().ajax.reload(); // Обновляем таблицу
                    },
                    error: function (error) {
                        Swal.fire('Ошибка!', 'Не удалось принять чек.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.reject-receipt-button', function () {
        const receiptId = $(this).data('id');

        Swal.fire({
            title: 'Подтверждение',
            text: 'Вы уверены, что хотите отклонить этот чек?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, отклонить',
            cancelButtonText: 'Отмена'
        }).then((result) => {
            if (result.isConfirmed) {
                // Здесь добавьте AJAX запрос для отклонения чека
                $.ajax({
                    url: `/api/receipts/${receiptId}/reject`, // Укажите правильный URL для отклонения чека
                    type: 'POST',
                    success: function (response) {
                        Swal.fire('Успешно!', 'Чек отклонен.', 'success');
                        $('#user-investments').DataTable().ajax.reload(); // Обновляем таблицу
                    },
                    error: function (error) {
                        Swal.fire('Ошибка!', 'Не удалось отклонить чек.', 'error');
                    }
                });
            }
        });
    });

    let currentAction = null;  // Переменная для хранения текущего действия (выплата или отмена)
    let currentInvestmentId = null;  // Переменная для хранения ID текущей инвестиции

    // Обработчик для кнопки "Выплата"
    $(document).on('click', '.payout-button', function () {
        const investmentId = $(this).data('id');

        Swal.fire({
            title: 'Подтверждение',
            text: 'Вы уверены, что хотит выплатить эту инвестицию?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, выплатить',
            cancelButtonText: 'Отмена'
        }).then((result) => {
            if (result.isConfirmed) {
                // Показываем индикатор загрузки
                Swal.fire({
                    title: 'Выполнение выплаты...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Отправляем AJAX запрос
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
                            $('#user-investments').DataTable().ajax.reload();
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

    // Обработчи для кнопки "Отмена"
    $(document).on('click', '.cancel-button', function () {
        const investmentId = $(this).data('id');

        Swal.fire({
            title: 'Подтверждение',
            text: 'Вы уерены, что хоите отменить эту инвестицию?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, отменить',
            cancelButtonText: 'Нет'
        }).then((result) => {
            if (result.isConfirmed) {
                // Показываем индикатор загрузки
                Swal.fire({
                    title: 'Отмена инвестиции...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Отправляем AJAX запрос
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
                            $('#user-investments').DataTable().ajax.reload();
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

    // Добавляем обработчик для фильтра сумы
    $('#filterAmount').change(function () {
        const activeButton = $("input[name='page_selector']:checked").attr("id");

        if (activeButton === "investments_wait") {
            if (table) { // Проверяем существование table
                table.clear().destroy();
                loadInvestments();
            }
        }
        // Если активна вкладка истории, игнорируем изменение фильтра
    });

    function updateInvestments() {
        const filterAmount = parseInt($('#filterAmount').val(), 10) || 0;

        $.ajax({
            url: '/api/investments/active',
            type: 'POST',
            contentType: 'application/json',
            success: function (response) {
                // Убедимся, что мы работаем с массивом
                const data = Array.isArray(response) ? response : (response.data || []);

                const table = $('#user-investments').DataTable();
                const currentData = table.rows().data().toArray();

                // Фльтруем новые данные по сумме
                const filteredData = data.filter(row => {
                    let profit = parseFloat(row.amount) * (parseFloat(row.percent) / 100);
                    let sum = parseFloat(row.amount) + parseFloat(profit);
                    return filterAmount === 0 || sum === filterAmount;
                });

                // Преобразуем отфильтрованные данные в формат для поиска
                const newDataIds = filteredData.map(item => item.id);

                // Удаляем строки, которых больше нет в отфильтрованных данных
                table.rows().every(function() {
                    const rowData = this.data();
                    if (!newDataIds.includes(rowData.id)) {
                        this.remove();
                    }
                });
                table.draw(false);

                // Добавляем новые отфильтрованные строки
                filteredData.forEach(item => {
                    if (!currentData.some(row => row.id === item.id)) {
                        table.row.add(item).draw(false);
                    }
                });
            },
            error: function(xhr) {
                console.error('Ошибка при обновлении данных:', xhr);
            }
        });
    }

    // Добавьте обработчик изменения рамера окна для корректной работы при повороте устройства
    $(window).on('resize', function() {
        if (!isMobileDevice()) {
            // Закрываем все открытые строки при переходе на десттоп
            const table = $('#user-investments').DataTable();
            table.rows().every(function() {
                if (this.child.isShown()) {
                    this.child.hide();
                    $(this.node()).removeClass('shown');
                }
            });
        }
    });

    // Добавьте обработчик для кнопки частичной выплаты
    $(document).on('click', '.partial-payout-button', function () {
        const investmentId = $(this).data('id');
        const totalAmount = $(this).data('total-amount');
        const partialPayout = $(this).data('partial-payout') || 0;
        const availableAmount = totalAmount - partialPayout;
        Swal.fire({
            title: 'Частичная выплата',
            html: `
                <div class="mb-3">
                    <label>Сумма выплаты (доступно: ${availableAmount} ₽)</label>
                    <input type="number" class="form-control" id="amount"
                        placeholder="Введите сумму"
                        max="${availableAmount}"
                        step="0.01">
                </div>
                <div class="mb-3">
                    <label>Telegram ID получателя (необязательно)</label>
                    <input type="number" class="form-control" id="telegram_user_id" placeholder="Введите Telegram ID">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Создать выплату',
            cancelButtonText: 'Отмена',
            preConfirm: () => {
                const amount = parseFloat(document.getElementById('amount').value);
                const telegramUserId = document.getElementById('telegram_user_id').value;

                if (!amount || amount <= 0) {
                    Swal.showValidationMessage('Введите корректную сумму');
                    return false;
                }

                if (amount > availableAmount) {
                    Swal.showValidationMessage(`Максимальная доступная сумма: ${availableAmount} ₽`);
                    return false;
                }

                return { amount, telegramUserId };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Показываем индикатор загрузки
                Swal.fire({
                    title: 'Создание выплаты...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Выбираем URL и данные в зависимости от наличия telegram_user_id
                const url = result.value.telegramUserId
                    ? '/api/receipts/create-manual'
                    : `/api/investments/${investmentId}/partial-payout`;

                const requestData = result.value.telegramUserId
                    ? {
                        investment_id: investmentId,
                        amount: result.value.amount,
                        telegram_user_id: result.value.telegramUserId
                    }
                    : {
                        amount: result.value.amount
                    };

                // Отправляем запрос
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: requestData,
                    success: function(response) {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Выплата успешно создана',
                            icon: 'success'
                        }).then(() => {
                            $('#user-investments').DataTable().ajax.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: xhr.responseJSON?.message || 'Произошла ошибка при создании выплаты',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Обработчик для кнопки "Уточнить"
    $(document).on('click', '.clarify-button', function () {
        const investmentId = $(this).data('id');

        $.ajax({
            url: `/api/investments/${investmentId}/clarify`,
            type: 'POST',
            success: function(response) {
                Swal.fire('Успешно', 'Уведомление отправлено пользователю', 'success');
            },
            error: function(xhr) {
                Swal.fire('Ошибка', xhr.responseJSON?.message || 'Произошла ошибка', 'error');
            }
        });
    });

    // Перепривязываем обработчики при изменении ориентации экрана
    $(window).on('orientationchange resize', function() {
        setTimeout(bindSearchHandlers, 200);
    });

    // Очищаем таймер при уничтожении страницы
    $(window).on('unload', function() {
        if (searchTimer) {
            clearTimeout(searchTimer);
        }
    });

    // Добавляем обработчик для виртуальной клавиатуры на мобильных
    let windowHeight = $(window).height();
    $(window).on('resize', function() {
        if ($(window).height() < windowHeight) {
            // Клавиатура открыта
            bindSearchHandlers();
        } else {
            // Клавиатура закрыта
            windowHeight = $(window).height();
        }
    });
})

function startTimers() {
    setInterval(function () {
        // Ищем все элементы с классом elapsed-time для обновления таймера
        $('.elapsed-time').each(function () {
            const created_at = $(this).data('created');
            const updated_at = $(this).data('updated');
            const isFinished = $(this).data('is-finished');

            // Обновляем таймер только для тех инвестиций, которые не завершены (is_finished === 0)
            if (isFinished === 0) {
                const timeDisplay = getElapsedTime(created_at); // Рассчитываем врмя дл активных инвестиций
                $(this).html(timeDisplay);  // Обновляем текст таймера
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

    // Подсвечиваем, если рошло больш указанного врмени
    if (hours >= highlightLimit) {
        return `<span style="color: red;">${hours}:${minutes}:${seconds}</span>`;
    } else {
        return `${String(hours).padStart(2, '0')}:${minutes}:${seconds}`;
    }
}


