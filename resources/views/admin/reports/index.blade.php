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
            <h2>Reports & Analytics</h2>
            <p>Completion and passing-rate analytics per flying school.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Reports</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">School Performance Reports</p>
                    <p class="panel-subtitle">Generate and export report summaries based on grade sheets.</p>
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
                        @foreach ($schoolReports as $report)
                            <tr>
                                <td class="fw-semibold">{{ $report->provider_name }}</td>
                                <td>{{ $report->total_students }}</td>
                                <td>{{ $report->completed }}</td>
                                <td><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $report->completion_rate }}</span>
                                </td>
                                <td><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $report->passing_rate }}</span>
                                </td>
                                <td data-order="{{ $report->report_date }}">{{ $report->report_date }}</td>
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
    </script>
</body>

</html>
