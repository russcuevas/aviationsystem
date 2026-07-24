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
            <h2>Aircraft Logbooks</h2>
            <p>Read-only audit and validation view for aircraft logs.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Logbooks</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Logbook Audit Queue</p>
                    <p class="panel-subtitle">Review entries and flag inconsistencies.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="logbooksTable">
                    <thead>
                        <tr>
                            <th>Logbook ID</th>
                            <th>Aircraft</th>
                            <th>Training Provider</th>
                            <th>Last Entry</th>
                            <th>Recorded By</th>
                            <th>Validation</th>
                            <th>Inconsistency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="school-code">LB-2026-101</span></td>
                            <td>RP-C1721</td>
                            <td>PhilSCA Villamor</td>
                            <td data-order="2026-04-02">Apr 2, 2026</td>
                            <td>Capt. Ramon Villanueva</td>
                            <td><span class="school-status status-active">Validated</span></td>
                            <td><span class="school-status status-available">None</span></td>
                        </tr>
                        <tr>
                            <td><span class="school-code">LB-2026-108</span></td>
                            <td>RP-DA401</td>
                            <td>PhilSCA Villamor</td>
                            <td data-order="2026-04-01">Apr 1, 2026</td>
                            <td>Engr. Carlos Santos</td>
                            <td><span class="school-status status-onleave">For Review</span></td>
                            <td><span class="school-status status-maintenance">Flagged</span></td>
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
        const logbooksTableEl = document.getElementById('logbooksTable');
        if (logbooksTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(logbooksTableEl).DataTable({
                pageLength: 10,
                order: [
                    [3, 'desc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
