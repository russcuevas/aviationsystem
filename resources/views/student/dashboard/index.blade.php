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
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon cobalt"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($hoursRemaining, 1) }}</div>
                        <div class="stat-label">Hours Remaining</div>
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
                                @forelse($upcomingSchedules as $sched)
                                    <tr>
                                        <td data-order="{{ $sched->date }}">
                                            {{ \Carbon\Carbon::parse($sched->date)->format('M j, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                        </td>
                                        <td>{{ $sched->instructor_name ?? 'N/A' }}</td>
                                        <td>{{ $sched->aircraft_registration ?? 'N/A' }}</td>
                                        <td>{{ $sched->lesson_type }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No upcoming flight schedules
                                            found.</td>
                                    </tr>
                                @endforelse
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
                                    <th>Required Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainingSummary as $stage)
                                    <tr>
                                        <td>{{ $stage->stage }}</td>
                                        <td>
                                            @if (strtolower($stage->status) === 'completed')
                                                <span class="school-status status-active">Completed</span>
                                            @elseif(strtolower($stage->status) === 'in progress')
                                                <span class="school-status status-onleave">In Progress</span>
                                            @else
                                                <span class="school-status status-inactive">{{ $stage->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $stage->required_hours }} hrs</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No training stages recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        const studentUpcomingTable = document.getElementById('studentUpcomingTable');
        if (studentUpcomingTable && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(studentUpcomingTable).DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                autoWidth: false
            });
        }

        const studentSummaryTable = document.getElementById('studentSummaryTable');
        if (studentSummaryTable && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(studentSummaryTable).DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
