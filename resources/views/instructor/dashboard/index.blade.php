<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor - Dashboard</title>
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="page-header">
            <h2>Flight Instructor & Mechanic Dashboard</h2>
            <p>Overview of flight training schedules, flight hours, and aircraft maintenance logbooks.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Overview</span></div>
        </div>

        <!-- ── Navigation Tabs ── -->
        <ul class="nav nav-pills mb-4 gap-2 bg-white p-2 border rounded shadow-sm" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 py-2" id="main-tab" data-bs-toggle="pill"
                    data-bs-target="#main-dashboard" type="button" role="tab" aria-controls="main-dashboard"
                    aria-selected="true">
                    <i class="bi bi-grid-1x2-fill me-2"></i>Main Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 py-2" id="mechanic-tab" data-bs-toggle="pill"
                    data-bs-target="#mechanic-dashboard" type="button" role="tab" aria-controls="mechanic-dashboard"
                    aria-selected="false">
                    <i class="bi bi-tools me-2"></i>Mechanic Dashboard
                </button>
            </li>
        </ul>

        <!-- ── Tab Contents ── -->
        <div class="tab-content" id="dashboardTabContent">

            <!-- ================= TAB 1: MAIN DASHBOARD ================= -->
            <div class="tab-pane fade show active" id="main-dashboard" role="tabpanel" aria-labelledby="main-tab">
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon cobalt"><i class="bi bi-people"></i></div>
                            <div class="stat-body">
                                <div class="stat-value">{{ $assignedStudentsCount }}</div>
                                <div class="stat-label">Assigned Students</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon purple"><i class="bi bi-calendar-event"></i></div>
                            <div class="stat-body">
                                <div class="stat-value">{{ $todaysFlightsCount }}</div>
                                <div class="stat-label">Today's Scheduled Flights</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon green"><i class="bi bi-clock-history"></i></div>
                            <div class="stat-body">
                                <div class="stat-value">{{ number_format($monthlyHours, 1) }}</div>
                                <div class="stat-label">Hours This Month</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule Table -->
                <div class="panel mb-4">
                    <div class="panel-header">
                        <div>
                            <p class="panel-title">Today's Flight Schedule</p>
                            <p class="panel-subtitle">Flights scheduled for today requiring evaluation</p>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table" id="todaysScheduleTable">
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
                                @foreach ($todaysSchedules as $sched)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">
                                                {{ \Carbon\Carbon::parse($sched->start_time)->format('h:i A') }} -
                                                {{ \Carbon\Carbon::parse($sched->end_time)->format('h:i A') }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">{{ $sched->student_name }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $sched->aircraft_reg }}</span></td>
                                        <td>{{ $sched->lesson_type }}</td>
                                        <td>
                                            @if ($sched->status === 'Completed')
                                                <span class="school-status status-active">Completed</span>
                                            @elseif ($sched->status === 'Scheduled' || $sched->status === 'In Progress')
                                                <span class="school-status status-onleave">{{ $sched->status }}</span>
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

                <!-- Assigned Students Table -->
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="panel-title">Assigned Students</p>
                            <p class="panel-subtitle">Students currently assigned under your flight instruction</p>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table" id="assignedStudentsTable">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Current Lesson</th>
                                    <th>Aircraft</th>
                                    <th>Next Scheduled Flight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignedStudents as $st)
                                    <tr>
                                        <td class="fw-semibold">{{ $st->student_name }}</td>
                                        <td>{{ $st->current_lesson }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $st->aircraft_reg }}</span></td>
                                        <td>
                                            @if ($st->next_flight_date)
                                                {{ \Carbon\Carbon::parse($st->next_flight_date)->format('M d, Y') }}
                                                <small class="text-muted">({{ \Carbon\Carbon::parse($st->next_flight_time)->format('h:i A') }})</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 2: MECHANIC DASHBOARD ================= -->
            <div class="tab-pane fade" id="mechanic-dashboard" role="tabpanel" aria-labelledby="mechanic-tab">
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon cobalt"><i class="bi bi-journal-bookmark"></i></div>
                            <div class="stat-body">
                                <div class="stat-value">{{ count($logbooks) }}</div>
                                <div class="stat-label">Total Logbook Logs</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon amber"><i class="bi bi-exclamation-triangle"></i></div>
                            <div class="stat-body">
                                @php
                                    $issuesCount = $logbooks->filter(fn($l) => !empty($l->technical_issues) && $l->technical_issues !== 'N/A')->count();
                                @endphp
                                <div class="stat-value">{{ $issuesCount }}</div>
                                <div class="stat-label">Technical Issues Logged</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon green"><i class="bi bi-tools"></i></div>
                            <div class="stat-body">
                                @php
                                    $mechanicsCount = $logbooks->filter(fn($l) => !empty($l->mechanics) && $l->mechanics !== 'N/A')->count();
                                @endphp
                                <div class="stat-value">{{ $mechanicsCount }}</div>
                                <div class="stat-label">Corrective Actions Recorded</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="panel-title">Aircraft Maintenance & Technical Logbooks</p>
                            <p class="panel-subtitle">Edit technical issues and mechanic notes for corrective action.</p>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table" id="mechanicLogbookTable">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Aircraft</th>
                                    <th>Student</th>
                                    <th>Flight Time</th>
                                    <th>Technical Issues</th>
                                    <th>Mechanic Corrective Action</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logbooks as $log)
                                    <tr>
                                        <td data-order="{{ $log->date_time }}">
                                            {{ \Carbon\Carbon::parse($log->date_time)->format('M d, Y h:i A') }}
                                        </td>
                                        <td><span class="fw-semibold text-primary">{{ $log->aircraft }}</span></td>
                                        <td>{{ $log->student_name }}</td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $log->flight_time }} hrs</span></td>
                                        <td>
                                            @if (!empty($log->technical_issues) && $log->technical_issues !== 'N/A')
                                                <span class="badge bg-danger text-wrap text-start p-2" style="font-weight: 500;">
                                                    <i class="bi bi-exclamation-octagon me-1"></i>{{ $log->technical_issues }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-secondary border">No Issues Logged</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($log->mechanics) && $log->mechanics !== 'N/A')
                                                <span class="badge bg-success text-wrap text-start p-2" style="font-weight: 500;">
                                                    <i class="bi bi-wrench me-1"></i>{{ $log->mechanics }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border">Pending Corrective Action</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary btn-edit-maintenance text-nowrap" type="button"
                                                data-id="{{ $log->id }}"
                                                data-aircraft="{{ $log->aircraft }}"
                                                data-issues="{{ $log->technical_issues }}"
                                                data-mechanics="{{ $log->mechanics }}">
                                                <i class="bi bi-pencil-square me-1"></i> Edit Maintenance
                                            </button>
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

    <!-- Modal Edit Maintenance & Technical Issues -->
    <div class="modal fade" id="editMaintenanceModal" tabindex="-1" aria-labelledby="editMaintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editMaintenanceModalLabel">
                        <i class="bi bi-tools text-primary me-2"></i>Update Maintenance & Corrective Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMaintenanceForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Aircraft Registration</label>
                            <input type="text" class="form-control bg-light fw-bold text-primary" id="m_aircraft" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="m_technical_issues" class="form-label fw-semibold text-danger">
                                <i class="bi bi-exclamation-triangle me-1"></i>Discrepancy / Technical Issues
                            </label>
                            <textarea class="form-control" id="m_technical_issues" name="technical_issues" rows="3"
                                placeholder="Describe any technical malfunction or discrepancy reported during flight."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="m_mechanics" class="form-label fw-semibold text-success">
                                <i class="bi bi-wrench me-1"></i>Mechanic Corrective Action & Notes
                            </label>
                            <textarea class="form-control" id="m_mechanics" name="mechanics" rows="3"
                                placeholder="Enter mechanic maintenance actions, repairs, or inspections performed."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Maintenance Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        $(document).ready(function() {
            if (window.jQuery && window.jQuery.fn.DataTable) {
                if ($('#todaysScheduleTable').length) {
                    $('#todaysScheduleTable').DataTable({ pageLength: 5, autoWidth: false });
                }
                if ($('#assignedStudentsTable').length) {
                    $('#assignedStudentsTable').DataTable({ pageLength: 5, autoWidth: false });
                }
                if ($('#mechanicLogbookTable').length) {
                    $('#mechanicLogbookTable').DataTable({ pageLength: 10, order: [[0, 'desc']], autoWidth: false });
                }
            }

            // Edit Maintenance Modal trigger
            $(document).on('click', '.btn-edit-maintenance', function() {
                const id = $(this).data('id');
                const aircraft = $(this).data('aircraft');
                const issues = $(this).data('issues');
                const mechanics = $(this).data('mechanics');

                $('#m_aircraft').val(aircraft);
                $('#m_technical_issues').val(issues !== 'N/A' ? issues : '');
                $('#m_mechanics').val(mechanics !== 'N/A' ? mechanics : '');

                $('#editMaintenanceForm').attr('action', `/instructor/dashboard/maintenance/${id}`);
                $('#editMaintenanceModal').modal('show');
            });
        });
    </script>
</body>

</html>
