document.addEventListener('DOMContentLoaded', function () {
    let chartInstance;

    // Функция для получения количества дней в выбранном месяце
    function getDaysInMonth(year, month) {
        return new Array(31).fill('').map((_, i) => new Date(year, month - 1, i + 1).toISOString().slice(8, 10));
    }

    function updateDailyStatistics(monthYear) {
        const [year, month] = monthYear.split('-');

        fetch(`/api/stats/graph?year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                const days = getDaysInMonth(year, month);
                const chartData = {
                    labels: days,
                    datasets: [
                        {
                            label: 'Всего пользователей',
                            data: data.total_users,
                            borderColor: '#727cf5',
                            backgroundColor: '#727cf520',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y-users'
                        },
                        {
                            label: 'Активные пользователи',
                            data: data.active_users,
                            borderColor: '#0acf97',
                            backgroundColor: '#0acf9720',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y-users'
                        },
                        {
                            label: 'Пополнения RUB',
                            data: data.today_deposits_rub,
                            borderColor: '#fa5c7c',
                            backgroundColor: '#fa5c7c20',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y-rub'
                        },
                        {
                            label: 'Выплаты RUB',
                            data: data.paid_today_rub,
                            borderColor: '#39afd1',
                            backgroundColor: '#39afd120',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y-rub'
                        },
                        {
                            label: 'Пополнения TRX',
                            data: data.today_deposits_trx,
                            borderColor: '#ffbc00',
                            backgroundColor: '#ffbc0020',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y-trx'
                        },
                        {
                            label: 'Выплаты TRX',
                            data: data.paid_today_trx,
                            borderColor: '#6c757d',
                            backgroundColor: '#6c757d20',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y-trx'
                        }
                    ]
                };

                if (chartInstance) {
                    chartInstance.destroy();
                }

                const ctx = document.getElementById('statisticsChart').getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        height: 600,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        stacked: false,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            'y-users': {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Пользователи'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            'y-rub': {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'RUB'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            'y-trx': {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'TRX'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('ru-RU', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            });
    }

    // При загрузке страницы обновляем статистику за текущий месяц
    updateDailyStatistics(document.getElementById('month-year').value);

    // Обновляем статистику при изменении месяца
    document.getElementById('month-year').addEventListener('change', function () {
        updateDailyStatistics(this.value);
    });
});
