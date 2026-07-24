<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    @include('admin.components.left_sidebar')

    @include('admin.components.topbar')
    <main class="main-content">
        <span class="badge bg-primary px-3 py-2 mb-3" style="font-size: 0.9rem; font-weight: 600; border-radius: 8px; background-color: var(--cobalt) !important;">
            <i class="bi bi-geo-alt-fill me-1"></i>{{ $providerName }}
        </span>
        <div class="page-header">
            <h2>Admin Dashboard</h2>
            <p>Daily operations overview for scheduling, resources, and student performance.</p>
            <div class="page-breadcrumb"><i class="bi bi-house-door"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Overview</span></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-mortarboard"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($studentsCount) }}</div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-person-video3"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($instructorsCount) }}</div>
                        <div class="stat-label">Instructors</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-airplane"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($aircraftsCount) }}</div>
                        <div class="stat-label">Aircraft</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-stopwatch"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($todaysFlightsCount) }}</div>
                        <div class="stat-label">Today's Flight</div>
                    </div>
                </div>
            </div>
        </div>

        <section class="charts-section">
            <div class="row g-3 mb-3">
                <div class="col-xl-6">
                    <div class="chart-panel">
                        <div class="chart-panel-header">
                            <div>
                                <p class="chart-panel-title">Upcoming Schedules</p>
                                <p class="chart-panel-subtitle">Planned flight activities for the next sessions</p>
                            </div>
                        </div>
                        <div class="chart-canvas-wrap">
                            <canvas id="trainingStageChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="chart-panel">
                        <div class="chart-panel-header">
                            <div>
                                <p class="chart-panel-title">Aircraft Status</p>
                                <p class="chart-panel-subtitle">Current availability and operational condition</p>
                            </div>
                        </div>
                        <div class="chart-canvas-wrap">
                            <canvas id="flightHoursChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        // Redefine renderCharts to use dynamic database data
        window.renderCharts = function() {
            if (typeof Chart === 'undefined') {
                return;
            }

            destroyCharts();
            const colors = getThemeChartColors();

            const ctxStage = document.getElementById('trainingStageChart');
            const ctxAircraft = document.getElementById('flightHoursChart');

            // --- UPCOMING SCHEDULES BY LESSON TYPE CHART ---
            const scheduleLabels = {!! json_encode(array_column($schedulesStats, 'lesson_type')) !!};
            const scheduleCounts = {!! json_encode(array_column($schedulesStats, 'count')) !!};

            if (ctxStage) {
                charts.push(new Chart(ctxStage, {
                    type: 'bar',
                    data: {
                        labels: scheduleLabels.length ? scheduleLabels : ['Cross-Country', 'Solo Flying'],
                        datasets: [{
                            label: 'Number of Students',
                            data: scheduleCounts.length ? scheduleCounts : [0, 0],
                            backgroundColor: colors.cobalt,
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 44
                        }]
                    },
                    options: {
                        ...baseChartOptions(colors),
                        plugins: {
                            ...baseChartOptions(colors).plugins,
                            legend: { display: false }
                        }
                    }
                }));
            }

            // --- AIRCRAFT STATUS (FLIGHT HOURS) CHART ---
            const aircraftRaw = {!! json_encode($aircraftStats) !!};
            const aircraftLabels = aircraftRaw.map(ac => `${ac.registration} (${ac.model})`);
            const aircraftHours = aircraftRaw.map(ac => ac.total_hours);

            if (ctxAircraft) {
                charts.push(new Chart(ctxAircraft, {
                    type: 'bar',
                    data: {
                        labels: aircraftLabels.length ? aircraftLabels : ['No Aircraft'],
                        datasets: [{
                            label: 'Total Hours',
                            data: aircraftHours.length ? aircraftHours : [0],
                            backgroundColor: colors.cobalt,
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 44
                        }]
                    },
                    options: {
                        ...baseChartOptions(colors),
                        plugins: {
                            ...baseChartOptions(colors).plugins,
                            legend: { display: false }
                        }
                    }
                }));
            }
        };

        // Initialize/Render charts immediately with the new dynamic logic
        renderCharts();
    </script>
</body>

</html>
