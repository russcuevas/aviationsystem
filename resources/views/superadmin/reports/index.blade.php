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
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <!-- ================= SIDEBAR ================= -->
    @include('superadmin.components.left_sidebar')

    <!-- ================= TOPBAR ================= -->
    @include('superadmin.components.topbar')


    <main class="main-content">
        <div class="page-header">
            <h2>Reports & Analytics</h2>
            <p>Completion and passing-rate analytics per flying school.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Reports</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">School Performance Reports</p>
                    <p class="panel-subtitle">Generate and export report summaries.</p>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button class="btn-add-form" type="button"><i class="bi bi-filetype-pdf"></i> Export PDF</button>
                    <button class="btn-add-form" type="button"><i class="bi bi-file-earmark-excel"></i> Export
                        Excel</button>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table" id="reportsTable">
                    <thead>
                        <tr>
                            <th>School</th>
                            <th>Total Students</th>
                            <th>Completed</th>
                            <th>Completion Rate</th>
                            <th>Passing Rate</th>
                            <th>Report Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PhilSCA Villamor</td>
                            <td>320</td>
                            <td>254</td>
                            <td>79.4%</td>
                            <td>88.2%</td>
                            <td data-order="2026-04-02">Apr 2, 2026</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel" style="margin-top:24px;">
            <div class="panel-header" style="align-items:flex-start;">
                <div>
                    <p class="panel-title">Student Progress Monitoring</p>
                    <p class="panel-subtitle">View all students, completed modules, required flight hours, and
                        evaluations.</p>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table" id="studentProgressTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>School</th>
                            <th>Modules</th>
                            <th>Flight Hours</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Progress Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Dela Cruz</td>
                            <td>PhilSCA Villamor</td>
                            <td>8 / 10</td>
                            <td>176 / 250 hrs</td>
                            <td>A-</td>
                            <td><span class="school-status status-active">Active</span></td>
                            <td><span class="school-status status-available">On Track</span></td>
                        </tr>
                        <tr>
                            <td>Carlos Santos</td>
                            <td>PhilSCA Villamor</td>
                            <td>9 / 10</td>
                            <td>232 / 250 hrs</td>
                            <td>A</td>
                            <td><span class="school-status status-active">Active</span></td>
                            <td><span class="school-status status-active">Near Completion</span></td>
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
        const reportsTableEl = document.getElementById('reportsTable');
        if (reportsTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(reportsTableEl).DataTable({
                pageLength: 10,
                order: [
                    [5, 'desc']
                ],
                autoWidth: false
            });
        }

        const studentProgressTableEl = document.getElementById('studentProgressTable');

        if (studentProgressTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(studentProgressTableEl).DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
