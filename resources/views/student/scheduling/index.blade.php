<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    @include('student.components.left_sidebar')

    @include('student.components.topbar')

    <main class="main-content">
        <div class="page-header">
            <h2>Schedule Viewing</h2>
            <p>View your assigned flight schedules with instructor and aircraft details.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Schedule Viewing</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Assigned Flight Schedules</p>
                    <p class="panel-subtitle">Your upcoming and recent flight training sessions.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="studentScheduleTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Instructor</th>
                            <th>Aircraft</th>
                            <th>Route</th>
                            <th>Stage</th>
                            <th>Lesson</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $sched)
                            <tr>
                                <td data-order="{{ $sched->date }}">
                                    {{ \Carbon\Carbon::parse($sched->date)->format('M j, Y') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                </td>
                                <td>{{ $sched->instructor_name ?? 'N/A' }}</td>
                                <td>{{ $sched->aircraft_registration ?? 'N/A' }}</td>
                                <td>
                                    @if (!empty($sched->route))
                                        <span class="badge bg-light text-primary border" style="font-size:0.8rem;">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $sched->route }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $sched->stage_name }}</span>
                                </td>
                                <td class="fw-semibold text-dark">
                                    {{ $sched->lesson_type }}
                                </td>
                                <td>
                                    @if ($sched->status === 'Completed')
                                        <span class="school-status status-active"><i
                                                class="bi bi-check-circle-fill me-1"></i>Completed</span>
                                    @elseif ($sched->status === 'Completed (Pending Approval)' || $sched->status === 'For Review')
                                        <span
                                            class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"
                                            style="font-size:0.78rem;">
                                            <i class="bi bi-hourglass-split me-1"></i>Pending Approval
                                        </span>
                                    @elseif ($sched->status === 'Scheduled' || $sched->status === 'In Progress')
                                        <span class="school-status status-onleave"><i
                                                class="bi bi-clock me-1"></i>Scheduled</span>
                                    @else
                                        <span class="school-status status-inactive">{{ $sched->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($('#studentScheduleTable').length && $.fn.DataTable) {
                $('#studentScheduleTable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    autoWidth: false,
                    destroy: true,
                    language: {
                        emptyTable: "No flight schedules found."
                    }
                });
            }
        });
    </script>
</body>

</html>
