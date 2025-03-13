$(document).ready(function () {
    let table = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/telegram-users',
            method: 'GET',
            data: function(d) {
                return {
                    draw: d.draw,
                    start: d.start,
                    length: d.length,
                    'search[value]': d.search.value,
                    'order[0][column]': d.order[0].column,
                    'order[0][dir]': d.order[0].dir
                };
            }
        },
        pageLength: 50,
        deferRender: true,
        lengthChange: true,
        dom: '<"d-flex justify-content-end align-items-center mb-3"<"me-2"l>f>rtip',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        columns: [
            {
                data: 'telegram_user_id',
                className: 'all',
                responsivePriority: 1
            },
            {
                data: null,
                className: 'all user-combined',
                responsivePriority: 2,
                render: function(data) {
                    let name = data.telegram_name || '';
                    let login = data.telegram_login ? ` (@${data.telegram_login})` : '';
                    return name + login;
                }
            },
            {
                data: 'telegram_name',
                className: 'all user-name',
                responsivePriority: 2
            },
            {
                data: 'telegram_login',
                className: 'all user-login',
                responsivePriority: 3
            },
            {
                data: 'instagram',
                className: 'min-w-xl',
                responsivePriority: 6,
                render: function(data) {
                    if (!data) return 'Не указан';
                    return `<a href="https://instagram.com/${data}" target="_blank">@${data}</a>`;
                }
            },
            {
                data: 'referrals_count',
                className: 'min-w-lg',
                responsivePriority: 5
            },
            {
                data: 'active_investmnet',
                className: 'min-w-md',
                responsivePriority: 4,
                render: function(data) {
                    return data ? `${parseInt(data).toLocaleString('ru-RU')} ₽` : '0 ₽';
                }
            },
            {
                data: 'referral_balance',
                className: 'min-w-xl',
                responsivePriority: 7,
                render: function(data) {
                    return `${parseInt(data).toLocaleString('ru-RU')} ₽`;
                }
            }
        ],
        order: [[0, 'desc']],
        responsive: {
            details: false
        },
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json'
        },
        search: {
            return: true
        },
    });

    $('#user-table tbody').on('click', 'tr', function(e) {
        let data = table.row(this).data();
        if (data && data.id) {
            window.location.href = `/admin/users/${data.id}`;
        }
    });

    // Функция для управления видимостью колонок
    function updateColumnsVisibility() {
        if ($(window).width() <= 500) {
            $('.user-name, .user-login').hide();
            $('.user-combined').show();
        } else {
            $('.user-combined').hide();
            $('.user-name, .user-login').show();
        }
        // Принудительный перерасчет ширины колонок
        table.columns.adjust();
    }

    // Обработчик события сортировки и перехода на новую страницу
    table.on('order.dt draw.dt', function() {
        updateColumnsVisibility();
    });

    // Обработчик изменения размера окна
    $(window).resize(function() {
        updateColumnsVisibility();
    });

    // Инициализация
    table.one('init', function() {
        updateColumnsVisibility();
    });
});
