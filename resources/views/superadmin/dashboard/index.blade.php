<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Superadmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <!-- ================= SIDEBAR ================= -->
    @include('superadmin.components.left_sidebar')

    <!-- ================= TOPBAR ================= -->
    @include('superadmin.components.topbar')

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2>System Overview</h2>
            <p>Monitoring student progress and regulatory compliance across training providers.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-house-door"></i>
                Dashboard
                <i class="bi bi-chevron-right"></i>
                <span>Overview</span>
            </div>
        </div>

        <!-- ── System Overall Totals Stat Cards ── -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon cobalt"><i class="bi bi-building"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $totalSchools }}</div>
                        <div class="stat-label">Flying Schools</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-person-video3"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $totalInstructors }}</div>
                        <div class="stat-label">Instructors</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-mortarboard"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($totalStudents) }}</div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-airplane"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $totalAircraft }}</div>
                        <div class="stat-label">Total Aircraft</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Dynamic Per Flying School Stat Cards ── -->
        @foreach ($schoolBreakdowns as $school)
            <div class="page-header mt-4 mb-2">
                <h2 style="font-size: 20px"><i class="bi bi-geo-alt-fill text-primary me-2"></i>{{ $school->name }}</h2>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="bi bi-mortarboard"></i></div>
                        <div class="stat-body">
                            <div class="stat-value">{{ $school->total_students }}</div>
                            <div class="stat-label">Students</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="bi bi-person-video3"></i></div>
                        <div class="stat-body">
                            <div class="stat-value">{{ $school->total_instructors }}</div>
                            <div class="stat-label">Instructors</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon amber"><i class="bi bi-airplane"></i></div>
                        <div class="stat-body">
                            <div class="stat-value">{{ $school->total_aircraft }}</div>
                            <div class="stat-label">Total Aircraft</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon cobalt"><i class="bi bi-check-circle-fill"></i></div>
                        <div class="stat-body">
                            <div class="stat-value">{{ $school->completed_students }}</div>
                            <div class="stat-label">Completed Students</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- ── Training Progress Table ── -->
        <div class="panel mt-4">
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
                            <th>Instructors</th>
                            <th>Total Aircraft</th>
                            <th>Completed Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schoolBreakdowns as $school)
                            <tr>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-name fw-bold">{{ $school->name }}</div>
                                    </div>
                                </td>
                                <td>{{ $school->total_students }}</td>
                                <td>{{ $school->total_instructors }}</td>
                                <td>{{ $school->total_aircraft }}</td>
                                <td><span class="badge bg-success px-2 py-1">{{ $school->completed_students }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        const trainingTableElement = document.getElementById('trainingTable');
        if (trainingTableElement && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(trainingTableElement).DataTable({
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
                autoWidth: false,
            });
        }
    </script>
</body>

</html>
