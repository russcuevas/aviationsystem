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
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
    @include('admin.components.left_sidebar')

    @include('admin.components.topbar')

    <main class="main-content">
        @if (isset($providerName))
            <span class="badge bg-primary px-3 py-2 mb-3"
                style="font-size: 0.9rem; font-weight: 600; border-radius: 8px; background-color: var(--cobalt) !important;">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ $providerName }}
            </span>
        @endif
        <div class="page-header">
            <h2>Grade Sheet Management</h2>
            <p>Review submitted grades and finalize student evaluations.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Grade Sheets</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Instructor Grade Submissions</p>
                    <p class="panel-subtitle">Finalize results after review.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="gradeAdminTable">
                    <thead>
                        <tr>
                            <th>Sheet ID</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Last Lesson</th>
                            <th>Time out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="school-code">GS-2026-301</span></td>
                            <td>Juan Dela Cruz</td>
                            <td>Capt. Ramon Villanueva</td>
                            <td>Ground School</td>
                            <td>88</td>
                            <td><span class="school-status status-onleave">For review</span></td>
                        </tr>
                        <tr>
                            <td><span class="school-code">GS-2026-302</span></td>
                            <td>Maria Reyes</td>
                            <td>Lt. Maria Reyes</td>
                            <td>Cross Country</td>
                            <td>92</td>
                            <td><span class="school-status status-active">Finalized</span></td>
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
        const gradeAdminTableEl = document.getElementById('gradeAdminTable');
        if (gradeAdminTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(gradeAdminTableEl).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
