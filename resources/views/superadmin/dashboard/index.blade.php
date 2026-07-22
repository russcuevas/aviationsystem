<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <!-- â• â• â• â• â• â• â• â• â• â• â• â• â• â• â•  SIDEBAR â• â• â• â• â• â• â• â• â• â• â• â• â• â• â•  -->
    @include('superadmin.components.left_sidebar')

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• TOPBAR â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    @include('superadmin.components.topbar')

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• MAIN CONTENT â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2>System Overview</h2>
            <p>Monitoring student progress and regulatory compliance across PhilSCA branches.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-house-door"></i>
                Dashboard
                <i class="bi bi-chevron-right"></i>
                <span>Overview</span>
            </div>
        </div>

        <!-- â”€â”€ Stat Cards â”€â”€ -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon cobalt"><i class="bi bi-building"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">12</div>
                        <div class="stat-label">Flying Schools</div>
                    </div>
                    <div class="stat-trend">
                        <span class="trend-badge trend-up"><i class="bi bi-arrow-up-short"></i>2</span>
                        <span class="trend-period">vs last yr</span>
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
                    <div class="stat-trend">
                        <span class="trend-badge trend-up"><i class="bi bi-arrow-up-short"></i>5</span>
                        <span class="trend-period">vs last yr</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-mortarboard"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">1,240</div>
                        <div class="stat-label">Total Students</div>
                    </div>
                    <div class="stat-trend">
                        <span class="trend-badge trend-up"><i class="bi bi-arrow-up-short"></i>8%</span>
                        <span class="trend-period">this sem</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-airplane"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">15</div>
                        <div class="stat-label">Total Aircraft</div>
                    </div>
                    <div class="stat-trend">
                        <span class="trend-badge trend-up"><i class="bi bi-arrow-up-short"></i>12%</span>
                        <span class="trend-period">this sem</span>
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
                                <p class="chart-panel-title">Students by Training Stage</p>
                                <p class="chart-panel-subtitle">Current distribution across active students</p>
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
                                <p class="chart-panel-title">Monthly Flight Hours</p>
                                <p class="chart-panel-subtitle">Comparison of logged hours for the last six months</p>
                            </div>
                        </div>
                        <div class="chart-canvas-wrap">
                            <canvas id="flightHoursChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-panel">
                <div class="chart-panel-header">
                    <div>
                        <p class="chart-panel-title">School Performance Overview</p>
                        <p class="chart-panel-subtitle">Instructor and student count</p>
                    </div>
                </div>
                <div class="chart-canvas-wrap tall">
                    <canvas id="schoolOverviewChart"></canvas>
                </div>
            </div>
        </section>

        <!-- â”€â”€ Recent Training Progress Table â”€â”€ -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">School Progress Breakdown</p>
                    <p class="panel-subtitle">Overall student completion progress by flying school branch</p>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table" id="trainingTable">
                    <thead>
                        <tr>
                            <th>Flying School</th>
                            <th>Total Students</th>
                            <th>Total Instructors</th>
                            <th>School Branch</th>
                            <th class="progress-cell">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="student-cell">
                                    <div class="student-avatar"
                                        style="background:linear-gradient(135deg,#004AAD,#3379d4)">PV</div>
                                    <div>
                                        <div class="student-name">PhilSCA Villamor</div>
                                        <div class="student-id">Main Training Campus</div>
                                    </div>
                                </div>
                            </td>
                            <td>320</td>
                            <td>24</td>
                            <td><span class="branch-pill"><i class="bi bi-geo-alt-fill"
                                        style="font-size:0.65rem"></i>Pasay City</span></td>
                            <td>
                                <div class="progress-wrap">
                                    <div class="progress-bar-track">
                                        <div class="progress-bar-fill" style="width:79%"></div>
                                    </div>
                                    <span class="progress-pct">79%</span>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

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
                    [4, 'desc']
                ],
                autoWidth: false,
            });
        }


        initTrainingDataTable();
    </script>
</body>

</html>
