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
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
    <!-- ================= SIDEBAR ================= -->
    @include('superadmin.components.left_sidebar')

    <!-- ================= TOPBAR ================= -->
    @include('superadmin.components.topbar')

    <main class="main-content">
        <div class="page-header">
            <h2>Flight Hours</h2>
            <p>Superadmin view-only monitoring of approved and cancelled flight hours.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-grid-1x2-fill"></i>
                Overview
                <i class="bi bi-chevron-right"></i>
                <span>Flight Hours</span>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Flight Hours Log</p>
                    <p class="panel-subtitle">View-only table for superadmin users.</p>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table flight-hours-table" id="flightHoursTable">
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Aircraft</th>
                            <th>Dual Inst.</th>
                            <th>PIC Time</th>
                            <th>Solo Time</th>
                            <th>Inst. Flight</th>
                            <th>Total Time</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($flightHours as $hour)
                            @if (in_array($hour->status, ['approved', 'cancelled']))
                                <tr>
                                    <td><span class="fw-semibold text-primary">{{ $hour->log_id }}</span></td>
                                    <td data-order="{{ $hour->created_at ? $hour->created_at->format('Y-m-d') : '' }}">
                                        <span
                                            class="fh-date">{{ $hour->created_at ? $hour->created_at->format('d/m/Y') : '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="fh-student">
                                            <span
                                                class="fh-student-name">{{ $hour->student ? $hour->student->first_name . ' ' . $hour->student->last_name : 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td><span
                                            class="fh-aircraft">{{ $hour->aircraft ? $hour->aircraft->registration : 'N/A' }}</span>
                                    </td>
                                    <td><span
                                            class="fh-hours">{{ $hour->dual_instruction_time !== null ? number_format($hour->dual_instruction_time, 1) . ' hrs' : '-' }}</span>
                                    </td>
                                    <td><span
                                            class="fh-hours">{{ $hour->pic_time !== null ? number_format($hour->pic_time, 1) . ' hrs' : '-' }}</span>
                                    </td>
                                    <td><span
                                            class="fh-hours">{{ $hour->solo_time !== null ? number_format($hour->solo_time, 1) . ' hrs' : '-' }}</span>
                                    </td>
                                    <td><span
                                            class="fh-hours">{{ $hour->instrument_flight_time !== null ? number_format($hour->instrument_flight_time, 1) . ' hrs' : '-' }}</span>
                                    </td>
                                    <td><span class="fh-hours"><strong>{{ number_format($hour->total_time, 1) }}
                                                hrs</strong></span></td>
                                    <td>
                                        @if ($hour->status === 'approved')
                                            <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>approved</span>
                                        @elseif ($hour->status === 'cancelled')
                                            <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>cancelled</span>
                                        @else
                                            <span class="badge bg-secondary px-2 py-1">{{ $hour->status }}</span>
                                        @endif
                                    </td>
                                    <td><span class="fh-remarks">{{ $hour->remarks ?? '-' }}</span></td>
                                </tr>
                            @endif
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
        const flightHoursTableElement = document.getElementById('flightHoursTable');
        let flightHoursDataTable;

        function initFlightHoursTable() {
            if (!flightHoursTableElement || !window.jQuery || !window.jQuery.fn.DataTable) {
                return;
            }

            if (flightHoursDataTable) {
                flightHoursDataTable.destroy();
            }

            flightHoursDataTable = window.jQuery(flightHoursTableElement).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false,
            });
        }

        initFlightHoursTable();
    </script>
</body>

</html>
