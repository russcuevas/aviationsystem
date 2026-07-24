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
            <h2>Grade Sheet Submission</h2>
            <p>Evaluate student performance and submit grade sheets for approval.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Grade Sheet Submission</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Submitted Grade Sheets</p>
                    <p class="panel-subtitle">Recently submitted evaluations and review status.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorGradeTable">
                    <thead>
                        <tr>
                            <th>Sheet ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Module</th>
                            <th>Evaluator</th>
                            <th>Status</th>
                            <th>Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="school-code">GS-I-2026-041</span></td>
                            <td data-order="2026-04-03">Apr 3, 2026</td>
                            <td>Juan Dela Cruz</td>
                            <td>Takeoff and Landing</td>
                            <td>Capt Ramon Villanueva</td>
                            <td><span class="school-status status-onleave">Validated</span></td>
                            <td><span class="school-status status-onleave">Flag</span></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('style.css') }}"></script>
    <script>
        const instructorGradeTableEl = document.getElementById('instructorGradeTable');
        if (instructorGradeTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(instructorGradeTableEl).DataTable({
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
