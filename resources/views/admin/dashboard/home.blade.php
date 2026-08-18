@extends('admin.layout.master')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .dashboard-card {
            transition: transform 0.2s;
            border-radius: 12px;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .card-link {
            text-decoration: none;
            display: block;
        }

        /* Responsive Chart Container */
        .chart-box {
            position: relative;
            width: 100%;
            height: 500px;
        }

        @media (max-width: 768px) {
            .chart-box {
                height: 250px;
            }
        }

        @media (max-width: 576px) {
            .chart-box {
                height: 200px;
            }
        }
    </style>

    <div class="gap-2 mb-3 d-flex">
        <button onclick="exportFullDashboard()" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-title="{{ __('Click to Pdf') }}"><i class="bi bi-file-pdf"></i>
            {{ __('Export to Pdf') }}</button>
        <a href="{{ route('admin.exportDashboard', ['year' => $selectedYear]) }}" class="btn btn-success"
            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click to Excel') }}"><i
                class="bi bi-file-earmark-excel"></i>
            {{ __('Export to Excel') }}</a>
    </div>

    <div id="printable-dashboard" style="background:white; padding: 20px;">
        {{-- Dashboard Cards Area --}}
        <div class="mb-4 row g-3">
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('list#userDetails') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click go to User Lists') }}">
                    <div
                        class="p-3 text-center shadow-lg border-1 border-dark text-dark card bg-light-subtle dashboard-card">
                        <h6><i class="bi bi-people"></i> {{ __('Total Users') }} </h6>
                        <h4 class="fw-bold">{{ $totalMembers }} {{ __('users') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('request#userDetails') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{ __('Click go to Pending User Lists') }}">
                    <div
                        class="p-3 text-center shadow-lg card bg-light border-1 border-warning dashboard-card text-warning">
                        <h6><i class="bi bi-person-plus"></i> {{ __('Request Users') }}</h6>
                        <h4 class="fw-bold">{{ $pendingCount }} {{ __('users') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('list#categoryDetails') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click go to Subject Lists') }}">
                    <div
                        class="p-3 text-center shadow-lg card bg-secondary-subtle text-dark border-1 border-secondary dashboard-card">
                        <h6><i class="bi bi-tag"></i> {{ __('Subject Details') }}</h6>
                        <h4 class="fw-bold">{{ $categoryCount }} {{ __('Kind') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('admin#bookingList') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{ __('Click go to books booking lists') }}">
                    <div
                        class="p-3 text-center shadow-lg border-1 border-warning card bg-warning-subtle text-dark dashboard-card">
                        <h6><i class="bi bi-bookmark-check"></i> {{ __('Bookings') }}</h6>
                        <h4 class="fw-bold">{{ __('Book') }} {{ $pendingRequests }} {{ __('Books') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('list#bookDetails') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click go to books lists') }}">
                    <div
                        class="p-3 text-center shadow-sm card bg-dark-subtle border-1 border-dark text-dark dashboard-card">
                        <h6><i class="bi bi-book"></i> {{ __('Books Details') }}</h6>
                        {{-- <h4 class="fw-bold">{{ __('books') }} {{ $bookCounts }} {{ __('Kind') }}</h4> --}}
                        <small class="text-nowrap">{{ __('Total') }}:
                            <span class="h2 text-primary fw-bold">{{ $totalBooks }}</span> {{ __('Books') }}|{{ __('Avail') }}: {{ $availableBooks }}
                            {{ __('Books') }}
                        </small>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('admin#borrowList') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click go to borrow lists') }}">
                    <div
                        class="p-3 text-center shadow-sm text-success card bg-success-subtle border-1 border-success dashboard-card">
                        <h6><i class="bi bi-journal-check"></i> {{ __('Borrowed') }}</h6>
                        <h4 class="fw-bold">{{ __('Book') }} {{ $activeBorrowers }} {{ __('Books') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('admin#returnedList') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click go to return lists') }}">
                    <div
                        class="p-3 text-center shadow-sm text-success card bg-success-subtle border-1 border-success dashboard-card">
                        <h6><i class="bi bi-journal-check"></i> {{ __('Returned Books') }}</h6>
                        <h4 class="fw-bold">{{ __('Book') }} {{ $returnedBooks }} {{ __('Books') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('list#paymentDetails') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click go to payment lists') }}">
                    <div
                        class="p-3 text-center shadow-lg card bg-success-subtle text-success border-1 border-success dashboard-card">
                        <h6><i class="bi bi-cash-stack"></i> {{ __('Payment Accounts') }}</h6>
                        <h4 class="fw-bold">{{ $paymentCount }} {{ __('Account') }}</h4>
                    </div>
                </a></div>
            {{-- 2. System Settings --}}
            <div class="col-12 col-md-4 col-lg-3">
                <a href="{{ route('admin#settingPage') }}" class="card-link" data-bs-toggle="tooltip"
                    data-bs-placement="top" data-bs-title="{{ __('Click go to setting') }}">
                    <div
                        class="p-3 text-center shadow-lg card bg-primary-subtle border-1 border-primary text-primary dashboard-card h-100">
                        <h6><i class="bi bi-gear"></i> {{ __('System Settings') }}</h6>
                        <hr class="my-2">
                        <small class="mb-1 d-block">
                            {{ __('Fine') }}: <strong>{{ $settings['daily_fine_rate'] ?? 0 }}</strong>
                            {{ __('MMK') }} |
                            {{ __('Limit') }}: <strong>{{ $settings['max_borrow_limit'] ?? 0 }}</strong>
                            {{ __('Books') }}
                        </small>
                        <small class="d-block">
                            {{ __('Booking Expire Hours') }}: {{ $settings['booking_expire_hours'] ?? 0 }}
                            {{ __('Hours') }} |
                            {{ __('Borrow Duration Days') }}: {{ $settings['borrow_duration_days'] ?? 0 }}
                            {{ __('Days') }}
                        </small>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('admin#overdueList') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click go to overdue lists') }}">
                    <div
                        class="p-3 text-center border-2 shadow-lg text-danger card bg-danger-subtle border-danger dashboard-card">
                        <h6><i class="bi bi-clock-history"></i> {{ __('Overdue') }}</h6>
                        <h4 class="fw-bold">{{ __('Book') }} {{ $overdueCount }} {{ __('Books') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('admin.returnedFines') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{ __('Click go to returnedFines lists') }}">
                    <div
                        class="p-3 text-center border-2 shadow-lg text-danger card bg-danger-subtle border-danger dashboard-card">
                        <h6><i class="bi bi-cash-coin"></i> {{ __('Total Fine') }}</h6>
                        <h4 class="fw-bold">{{ number_format($totalFineAmount) }} {{ __('MMK') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('list#memberFees') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{ __('Click go to memberFees lists') }}">
                    <div
                        class="p-3 text-center border-2 shadow-lg text-primary card bg-primary-subtle border-primary dashboard-card">
                        <h6><i class="bi bi-cash-coin"></i> {{ __('Member Fees') }}</h6>
                        <h4 class="fw-bold">{{ number_format($totalMemberFees) }} {{ __('MMK') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('admin#lostBooksList') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{ __('Click go to lostBooks lists') }}">
                    <div
                        class="p-3 text-center border-2 shadow-lg text-primary card bg-warning-subtle border-warning dashboard-card">
                        <h6><i class="bi bi-cash-coin"></i> {{ __('Total Lost Fine') }}</h6>
                        <h4 class="fw-bold">{{ number_format($totalLostFineAmount) }} {{ __('MMK') }}</h4>
                    </div>
                </a></div>
            <div class="col-12 col-md-4 col-lg-3"><a href="{{ route('admin#damageBooksList') }}" class="card-link"
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{ __('Click go to damageBooks lists') }}">
                    <div
                        class="p-3 text-center border-2 shadow-lg text-primary card bg-warning-subtle border-warning dashboard-card">
                        <h6><i class="bi bi-cash-coin"></i> {{ __('Total Damage Fine') }}</h6>
                        <h4 class="fw-bold">{{ number_format($totalDamageFineAmount) }} {{ __('MMK') }}</h4>
                    </div>
                </a></div>
        </div>

        {{-- Charts --}}
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="p-3 shadow-lg card">
                    <h6 class="mb-3">{{ __('Borrowing Status Overview') }}</h6>
                    <div class="chart-box"><canvas id="statusPieChart"></canvas></div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-3 shadow-lg card">
                    <h6 class="mb-3">{{ __('Categories Distribution') }}</h6>
                    <div class="chart-box"><canvas id="categorySumChart"></canvas></div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-3 shadow-lg card">
                    <h6 class="mb-3">{{ __('Academic Year Lending Statistics') }}</h6>
                    <div class="chart-box"><canvas id="yearLendingChart"></canvas></div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-3 shadow-lg card">
                    <h6 class="mb-3">{{ __('Most Borrowed Categories') }}</h6>
                    <div class="chart-box"><canvas id="topCategoryChart"></canvas></div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="p-3 shadow-lg card">
                    <h6 class="mb-3">{{ __('Daily') }}</h6>
                    <div class="chart-box"><canvas id="dailyChart"></canvas></div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="p-3 shadow-lg card">
                    <div>
                        <form method="GET" action="{{ route('admin#home') }}">
                            <select name="year" onchange="this.form.submit()" class="border-none form-select">
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <h6 class="mb-3">{{ __('Monthly') }}</h6>
                    <div class="chart-box"><canvas id="monthlyChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script> --}}
    <script>
        // PDF Export လုပ်ရန် Function
        // function exportFullDashboard() {
        //     // Chart animation များက PDF ထဲမှာ ပုံမပေါ်မှာစိုးလို့ ခဏပိတ်ထားခြင်း
        //     Chart.helpers.each(Chart.instances, function(instance) {
        //         instance.options.animation = false;
        //     });

        //     html2canvas(document.querySelector("#printable-dashboard"), {
        //         scale: 2,
        //         useCORS: true
        //     }).then(canvas => {
        //         let image = canvas.toDataURL("image/png");
        //         let win = window.open('', '_blank');
        //         win.document.write('<html><body style="margin:0;"><img src="' + image +
        //             '" style="width:100%;"></body></html>');
        //         win.document.close();

        //         setTimeout(() => {
        //             win.print(); // Print Dialog ပွင့်လာပါမယ် (Save as PDF လုပ်ပါ)
        //         }, 500);
        //     });
        // }
        // PDF Export လုပ်ရန် Function (Updated)
function exportFullDashboard() {
    // Chart animation များကို ခဏပိတ်ရန် (Chart.js version အသစ်များနှင့် ကိုက်ညီစေရန် ပြင်ဆင်ထားသည်)
    if (window.Chart && window.Chart.instances) {
        Object.values(window.Chart.instances).forEach(instance => {
            if (instance.options) {
                instance.options.animation = false;
            }
        });
    }

    const element = document.querySelector("#printable-dashboard");

    html2canvas(element, {
        scale: 2,
        useCORS: true
    }).then(canvas => {
        let image = canvas.toDataURL("image/png");
        let printWindow = window.open('', '_blank');
        
        if (!printWindow) {
            alert('Please allow pop-ups for this website to export PDF.');
            return;
        }

        printWindow.document.write(`
            <html>
                <head>
                    <title>{{ __('Dashboard Export') }}</title>
                    <style>
                        body { margin: 0; text-align: center; }
                        img { width: 100%; height: auto; }
                    </style>
                </head>
                <body>
                    <img src="${image}" />
                </body>
            </html>
        `);
        printWindow.document.close();

        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.focus();
                printWindow.print(); // Print Dialog ပွင့်လာပါမယ် (Save as PDF လုပ်ပါ)
            }, 500);
        };
    });
}
        //Chart.register(ChartDataLabels);

        document.addEventListener("DOMContentLoaded", function() {
            // Shared Bar Options for Responsiveness
            const getBarOptions = (xTitle, yTitle) => ({
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: xTitle,
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: yTitle,
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            });

            // 1. Pie Chart
            new window.Chart(document.getElementById('statusPieChart'), {
                type: 'pie',
                data: {
                    labels: ['{{ __('Borrowed') }}','{{ __('Returned Books') }}','{{ __('Overdue') }}', '{{ __('Paid Fines') }}', '{{ __('Lost Fines') }}', '{{ __('Damage Fines') }}'],
                    datasets: [{
                        data: [{{ $pieData['borrowed'] ?? 0 }}, {{ $pieData['returned'] ?? 0 }}, {{ $pieData['overdue'] ?? 0 }},
                            {{ $pieData['fine'] ?? 0 }},{{ $pieData['lost_fine'] ?? 0 }},
                            {{ $pieData['damage_fine'] ?? 0 }}
                        ],
                        backgroundColor: ['#28a745','#8142FF', '#FF4069', '#ffc107','#dc3545','#059BFF']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        datalabels: {
                            formatter: (value) => value > 0 ? value : "",
                            color: '#fff',
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            // 2. Doughnut Chart
            // new Chart(document.getElementById('categorySumChart'), {
            //     type: 'doughnut',
            //     data: {
            //         labels: {!! json_encode($categoryData->pluck('name')) !!},
            //         datasets: [{
            //             data: {!! json_encode($categoryData->pluck('total_books_qty')) !!},
            //             backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            //                 '#FF9F40'
            //             ]
            //         }]
            //     },
            //     options: {
            //         responsive: true,
            //         maintainAspectRatio: false,
            //         plugins: {
            //             legend: {
            //                 position: 'bottom',
            //                 align: 'center'
            //             },
            //             datalabels: {
            //                 color: '#FFFFFF',
            //                 font: {
            //                     weight: 'bold',
            //                     size: 14
            //                 },
            //                 formatter: (value) => value
            //             }
            //         }
            //     }
            // });

            // 3. Year Lending Chart
            new window.Chart(document.getElementById('categorySumChart'), {
    type: 'bar', // Doughnut မှ Bar သို့ ပြောင်း
    data: {
        labels: {!! json_encode($categoryData->pluck('name')) !!},
        datasets: [{
            label: 'စာအုပ်အရေအတွက်',
            data: {!! json_encode($categoryData->pluck('total_books_qty')) !!},
            // Data အရေအတွက်အလိုက် အရောင်တွေ auto သုံးပေးမယ့် logic
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
                '#FF9F40', '#E7E9ED', '#33FF57', '#3357FF', '#FF33A1'
            ],
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y', // Horizontal Bar ဖြစ်စေရန်
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false // Bar chart မှာ Legend သိပ်မလိုတော့ဘူး
            },
            datalabels: {
                color: '#333', // Bar အပြင်ဘက်မှာ ပြမို့မို့ အနက်ရောင်/မီးခိုးရောင် သုံး
                anchor: 'end',
                align: 'right',
                font: {
                    weight: 'bold',
                    size: 12
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});
            new window.Chart(document.getElementById('yearLendingChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($yearLending->pluck('academic_year')) !!},
                    datasets: [{
                        label: '{{ __('Books Borrowed') }}',
                        data: {!! json_encode($yearLending->pluck('total')) !!},
                        backgroundColor: '#0dcaf0'
                    }]
                },
                options: {
                    ...getBarOptions('{{ __('Academic Year') }}', '{{ __('Borrowed Count') }}'),
                    plugins: {
                        ...getBarOptions('{{ __('Academic Year') }}', '{{ __('Borrowed Count') }}')
                        .plugins,
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    }
                }
            });

            // 4. Daily Chart
            new window.Chart(document.getElementById('dailyChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dailyBorrowing->pluck('date')) !!},
                    datasets: [{
                        label: '{{ __('Daily') }}',
                        data: {!! json_encode($dailyBorrowing->pluck('total')) !!},
                        backgroundColor: '#ff6384'
                    }]
                },
                options: {
                    ...getBarOptions('{{ __('7 Days') }}', '{{ __('Borrowed Count') }}'),
                    plugins: {
                        ...getBarOptions('{{ __('7 Days') }}', '{{ __('Borrowed Count') }}').plugins,
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    }
                }
            });

            // 5. Monthly Chart
            new window.Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    datasets: [{
                        label: '{{ __('Monthly') }}',
                        // Use the formatted $chartData array directly
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: '#36a2eb'
                    }]
                },
                options: {
                    ...getBarOptions('{{ __('12 Months') }}', '{{ __('Borrowed Count') }}'),
                    plugins: {
                        ...getBarOptions('{{ __('12 Months') }}', '{{ __('Borrowed Count') }}').plugins,
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    }
                }
            });

            // 6. Top Category Chart
            new window.Chart(document.getElementById('topCategoryChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($topCategories->pluck('name')) !!},
                    datasets: [{
                        label: '{{ __('Borrowed Count') }}',
                        data: {!! json_encode($topCategories->pluck('total_borrowed')) !!},
                        backgroundColor: '#20c997'
                    }]
                },
                options: {
                    ...getBarOptions('{{ __('Category') }}', '{{ __('Borrowed Count') }}'),
                    plugins: {
                        ...getBarOptions('{{ __('Category') }}', '{{ __('Borrowed Count') }}').plugins,
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
