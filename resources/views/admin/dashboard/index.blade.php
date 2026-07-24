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
                        <div class="stat-value">1,240</div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-person-video3"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">84</div>
                        <div class="stat-label">Instructors</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-airplane"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">15</div>
                        <div class="stat-label">Aircraft</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-stopwatch"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">42</div>
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
        const trainingTableElement = document.getElementById('trainingTable');
        let trainingDataTable;

        function initTrainingDataTable() {
            if (!trainingTableElement || !window.jQuery || !window.jQuery.fn.DataTable) {
                return;
            }

            if (trainingDataTable) {
                trainingDataTable.destroy();
            }

            trainingDataTable = window.jQuery(trainingTableElement).DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                autoWidth: false,
            });
        }

        initTrainingDataTable();
    </script>
</body>

</html>
