<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    @include('instructor.components.left_sidebar')

    @include('instructor.components.topbar')

    <main class="main-content">
        @if (isset($providerName))
            <span class="badge bg-primary px-3 py-2 mb-3"
                style="font-size: 0.9rem; font-weight: 600; border-radius: 8px; background-color: var(--cobalt) !important;">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ $providerName }}
            </span>
        @endif

        <div class="page-header">
            <h2>Instructor Dashboard</h2>
            <p>Monitor assigned students and your daily or weekly flight schedule.</p>
            <div class="page-breadcrumb"><i class="bi bi-house-door"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Overview</span></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-people"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($assignedStudentsCount ?? 0) }}</div>
                        <div class="stat-label">Assigned Students</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-calendar-check"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($todaysFlightsCount ?? 0) }}</div>
                        <div class="stat-label">Flights Today</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($monthlyHours ?? 0, 1) }}</div>
                        <div class="stat-label">Hours This Month</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="panel h-100">
                    <div class="panel-header">
                        <div>
                            <p class="panel-title">Today's Schedule</p>
                            <p class="panel-subtitle">Your assigned flight sessions for today.</p>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table" id="todayScheduleTable">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Student</th>
                                    <th>Aircraft</th>
                                    <th>Lesson</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                 @forelse($todaysSchedules as $sched)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sched->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($sched->end_time)->format('h:i A') }}</td>
                                    <td class="fw-semibold">{{ $sched->student_name }}</td>
                                    <td>{{ $sched->aircraft_reg }}</td>
                                    <td>{{ $sched->lesson_type }}</td>
                                    <td>
                                        @if($sched->status === 'Completed')
                                            <span class="school-status status-active">Completed</span>
                                        @elseif($sched->status === 'Scheduled')
                                            <span class="school-status status-onleave">Scheduled</span>
                                        @else
                                            <span class="school-status status-inactive">{{ $sched->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No flights scheduled for today.</td>
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
                            <p class="panel-title">My Students</p>
                            <p class="panel-subtitle">Quick view of your assigned students and recent sessions.</p>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table" id="assignedStudentsTable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Lesson</th>
                                    <th>Date</th>
                                    <th>Aircraft</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedStudents as $st)
                                <tr>
                                    <td class="fw-semibold">{{ $st->student_name }}</td>
                                    <td>{{ $st->current_lesson }}</td>
                                    <td data-order="{{ $st->next_flight_date }} {{ $st->next_flight_time }}">{{ \Carbon\Carbon::parse($st->next_flight_date)->format('M d, Y') }} {{ \Carbon\Carbon::parse($st->next_flight_time)->format('h:i A') }}</td>
                                    <td>{{ $st->aircraft_reg }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No assigned students found.</td>
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
        const todayScheduleTableEl = document.getElementById('todayScheduleTable');
        if (todayScheduleTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(todayScheduleTableEl).DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                autoWidth: false
            });
        }

        const assignedStudentsTableEl = document.getElementById('assignedStudentsTable');
        if (assignedStudentsTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(assignedStudentsTableEl).DataTable({
                pageLength: 10,
                order: [
                    [2, 'desc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
