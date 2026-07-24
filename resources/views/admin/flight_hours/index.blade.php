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
            <h2>Flight Hours Validation</h2>
            <p>Review and confirm logged flight hours entries.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Flight Hours Validation</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Validation Queue</p>
                    <p class="panel-subtitle">Approve or return submitted flight-hour logs.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="validationTable">
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Aircraft</th>
                            <th>Dual</th>
                            <th>Pic</th>
                            <th>Review Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="school-code">FH-2026-201</span></td>
                            <td data-order="2026-04-02">Apr 2, 2026</td>
                            <td>Juan Dela Cruz</td>
                            <td>RP-C1721</td>
                            <td>1 hours</td>
                            <td>1.5 hours</td>
                            <td><span class="school-status status-onleave">Pending Review</span></td>
                        </tr>
                        <tr>
                            <td><span class="school-code">FH-2026-202</span></td>
                            <td data-order="2026-04-01">Apr 1, 2026</td>
                            <td>Maria Reyes</td>
                            <td>RP-PA281</td>
                            <td>2.0 hours</td>
                            <td>2.5 hours</td>
                            <td><span class="school-status status-active">Confirmed</span></td>
                        </tr>
                        <tr>
                            <td><span class="school-code">FH-2026-203</span></td>
                            <td data-order="2026-03-31">Mar 31, 2026</td>
                            <td>Ana Lim</td>
                            <td>RP-C1508</td>
                            <td>1.1 hours</td>
                            <td>1.6 hours</td>
                            <td><span class="school-status status-inactive">Returned</span></td>
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
        const validationTableEl = document.getElementById('validationTable');
        if (validationTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(validationTableEl).DataTable({
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
