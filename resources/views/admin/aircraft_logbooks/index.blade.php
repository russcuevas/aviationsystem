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
            <h2>Aircraft Logbook Monitoring</h2>
            <p>Monitor aircraft usage, availability, and reported issues.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Aircraft Logbooks</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">New Logbook Entry Data</p>
                    <p class="panel-subtitle">Log entries captured from aircraft logbook form fields.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="adminLogbookTable">
                    <thead>
                        <tr>
                            <th>Aircraft</th>
                            <th>Date and Time</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Block off start</th>
                            <th>Take off</th>
                            <th>Landing</th>
                            <th>Block on off</th>
                            <th>Block time</th>
                            <th>Flight time</th>
                            <th>Fuel Used (gal)</th>
                            <th>Technical Issues</th>
                            <th>Mechanics</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>RP-C1721</td>
                            <td data-order="2026-04-02">02/04/2026</td>
                            <td>Capt. Ramon Villanueva</td>
                            <td>1.5</td>
                            <td>RPLL</td>
                            <td>RPLL</td>
                            <td>22</td>
                            <td><span class="school-status status-active">None</span></td>
                            <td>Dual circuit training and landing drills.</td>
                            <td>2</td>
                            <td>3</td>
                            <td>1</td>
                            <td>1</td>
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
        const adminLogbookTable = document.getElementById('adminLogbookTable');
        if (adminLogbookTable && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(adminLogbookTable).DataTable({
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
