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
            <h2>Student Dashboard</h2>
            <p>View your personal progress summary and upcoming flight schedules.</p>
            <div class="page-breadcrumb"><i class="bi bi-house-door"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Overview</span></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-clipboard-check"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $lessonsCompleted }}</div>
                        <div class="stat-label">Lessons Completed</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-calendar-event"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $upcomingFlightsCount }}</div>
                        <div class="stat-label">Upcoming Flights</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-stopwatch"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($totalFlightHours, 1) }}</div>
                        <div class="stat-label">Total Flight Hours</div>
                        <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                            {{ number_format($completedFlightHours, 1) }}h completed |
                            {{ number_format($scheduledFlightHours, 1) }}h scheduled
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon cobalt"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ (floor($requiredHours) == $requiredHours) ? (int)$requiredHours : number_format($requiredHours, 1) }}</div>
                        <div class="stat-label">Required Hours</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="panel h-100">
                    <div class="panel-header">
                        <div>
                            <p class="panel-title">Upcoming Schedule</p>
                            <p class="panel-subtitle">Your next assigned flight sessions.</p>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table" id="studentUpcomingTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Instructor</th>
                                    <th>Aircraft</th>
                                    <th>Lesson</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingSchedules as $sched)
                                    <tr>
                                        <td data-order="{{ $sched->date }}">
                                            {{ \Carbon\Carbon::parse($sched->date)->format('M j, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                        </td>
                                        <td>{{ $sched->instructor_name ?? 'N/A' }}</td>
                                        <td>{{ $sched->aircraft_registration ?? 'N/A' }}</td>
                                        <td>{{ $sched->lesson_type }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="panel h-100">
                    <div class="panel-header">
                        <div>
                            <p class="panel-title">Training Summary</p>
                            <p class="panel-subtitle">Completed and pending modules at a glance.</p>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table" id="studentSummaryTable">
                            <thead>
                                <tr>
                                    <th>Stage</th>
                                    <th>Status</th>
                                    <th>Completion Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trainingSummary as $stage)
                                    <tr>
                                        <td class="fw-semibold text-dark">{{ $stage->stage }}</td>
                                        <td>
                                            @if (strtolower($stage->status) === 'completed')
                                                <span class="school-status status-active"><i
                                                        class="bi bi-check-circle-fill me-1"></i>Completed</span>
                                            @elseif(strtolower($stage->status) === 'in progress')
                                                <span class="school-status status-onleave"><i
                                                        class="bi bi-hourglass-split me-1"></i>In Progress</span>
                                            @else
                                                <span class="school-status status-inactive">{{ $stage->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1"
                                                    style="height: 8px; border-radius: 4px; background-color: rgba(0,0,0,0.08);">
                                                    <div class="progress-bar {{ $stage->completion_percentage == 100 ? 'bg-success' : 'bg-primary' }}"
                                                        role="progressbar"
                                                        style="width: {{ $stage->completion_percentage }}%;"
                                                        aria-valuenow="{{ $stage->completion_percentage }}"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="fw-bold small">{{ $stage->completion_percentage }}%</span>
                                            </div>
                                            <div class="small text-muted mt-1" style="font-size: 0.78rem;">
                                                <i
                                                    class="bi bi-journal-check me-1"></i>{{ $stage->completed_lessons }}/{{ $stage->total_lessons }}
                                                Lessons | {{ $stage->required_hours }} hrs req.
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($('#studentUpcomingTable').length && $.fn.DataTable) {
                $('#studentUpcomingTable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    autoWidth: false,
                    destroy: true,
                    language: {
                        emptyTable: "No upcoming flight schedules found."
                    }
                });
            }

            if ($('#studentSummaryTable').length && $.fn.DataTable) {
                $('#studentSummaryTable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    autoWidth: false,
                    destroy: true,
                    language: {
                        emptyTable: "No training stages recorded."
                    }
                });
            }
        });
    </script>
</body>

</html>
