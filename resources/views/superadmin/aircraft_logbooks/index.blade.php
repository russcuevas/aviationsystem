<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Superadmin - Aircraft Logbooks</title>
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
            <p>Superadmin monitoring of recorded aircraft logbook entries across flight schools.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Logbooks</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Aircraft Logbook Entries</p>
                    <p class="panel-subtitle">Log entries captured from aircraft logbook records.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="superadminLogbookTable">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Aircraft</th>
                            <th>Training Provider</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Block Off / On</th>
                            <th>Take Off / Landing</th>
                            <th>Block Time</th>
                            <th>Flight Time</th>
                            <th>Fuel (gal)</th>
                            <th>Technical Issues</th>
                            <th>Mechanics</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logbooks as $log)
                            <tr>
                                <td data-order="{{ $log->date_time }}">
                                    {{ \Carbon\Carbon::parse($log->date_time)->format('M d, Y h:i A') }}
                                </td>
                                <td class="fw-semibold">{{ $log->aircraft }}</td>
                                <td><span class="badge bg-primary px-2 py-1" style="background-color: var(--cobalt) !important;">{{ $log->provider_name ?? 'N/A' }}</span></td>
                                <td>{{ $log->student_name }}</td>
                                <td>{{ $log->instructor_name }}</td>
                                <td class="text-nowrap"><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->block_off_start }} -
                                        {{ $log->block_on_off }}</span></td>
                                <td class="text-nowrap"><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->take_off }} -
                                        {{ $log->landing }}</span></td>
                                <td><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->block_time }}</span>
                                </td>
                                <td><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->flight_time }}</span>
                                </td>
                                <td>{{ $log->fuel_used_gal }}</td>
                                <td>{{ $log->technical_issues ?? 'N/A' }}</td>
                                <td>{{ $log->mechanics ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
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
        const superadminLogbookTable = document.getElementById('superadminLogbookTable');
        if (superadminLogbookTable && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(superadminLogbookTable).DataTable({
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
