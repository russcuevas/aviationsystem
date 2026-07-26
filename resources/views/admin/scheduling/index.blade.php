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
    <style>
        .status-scheduled {
            background-color: rgba(52, 152, 219, 0.15);
            color: #3498db;
        }

        .status-completed {
            background-color: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
        }

        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
        }
    </style>
</head>

<body>
    @include('admin.components.left_sidebar')

    @include('admin.components.topbar')

    <main class="main-content">
        <span class="badge bg-primary px-3 py-2 mb-3"
            style="font-size: 0.9rem; font-weight: 600; border-radius: 8px; background-color: var(--cobalt) !important;">
            <i class="bi bi-geo-alt-fill me-1"></i>{{ $providerName }}
        </span>

        <div class="page-header">
            <h2>Scheduling Management</h2>
            <p>Assign schedules and detect conflicts for students, instructors, and aircraft.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Overview</span></div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert"
                style="border-radius: var(--radius);">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert"
                style="border-radius: var(--radius);">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Flight Schedule Board</p>
                    <p class="panel-subtitle">Current schedule status for assigned flights.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
                    <i class="bi bi-plus-lg"></i>
                    New Schedule
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="scheduleTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Assigned Stages</th>
                            <th>Total Flight Schedules</th>
                            <th>Stage Completion Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php
                                $totalStages = count($student->stages_breakdown ?? []);
                                $completedStages = 0;
                                foreach ($student->stages_breakdown ?? [] as $stgItem) {
                                    if (($stgItem['status'] ?? '') === 'Completed') {
                                        $completedStages++;
                                    }
                                }
                            @endphp
                            <tr data-id="{{ $student->id }}">
                                <td>
                                    <span class="school-code">STU-{{ sprintf('%03d', $student->id) }}</span>
                                </td>
                                <td class="fw-bold text-dark">{{ $student->first_name }} {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}</td>
                                <td>
                                    @forelse($student->stages_breakdown ?? [] as $stgItem)
                                        <span class="badge bg-light text-dark border me-1">{{ $stgItem['stage'] }}</span>
                                    @empty
                                        <span class="text-muted small">No stage assigned</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1" style="font-size: 0.8rem;">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $student->schedules_count ?? 0 }} Flights Scheduled
                                    </span>
                                </td>
                                <td>
                                    @if($totalStages > 0 && $completedStages === $totalStages)
                                        <span class="school-status status-completed"><i class="bi bi-check-circle-fill me-1"></i>Completed ({{ $completedStages }}/{{ $totalStages }})</span>
                                    @elseif($completedStages > 0)
                                        <span class="school-status status-scheduled"><i class="bi bi-hourglass-split me-1"></i>In Progress ({{ $completedStages }}/{{ $totalStages }})</span>
                                    @else
                                        <span class="school-status status-scheduled"><i class="bi bi-clock me-1"></i>In Progress (0/{{ $totalStages }})</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary btn-view-student-breakdown"
                                            data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                                            data-schedules-count="{{ $student->schedules_count ?? 0 }}"
                                            data-breakdown='@json($student->stages_breakdown ?? [])'>
                                            <i class="bi bi-eye me-1"></i> View Breakdown
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#newScheduleModal"
                                            onclick="$('#scheduleStudent').val({{ $student->id }}).trigger('change');">
                                            <i class="bi bi-plus-lg me-1"></i> Add Schedule
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- New Schedule Modal -->
    <div class="modal fade" id="newScheduleModal" tabindex="-1" aria-labelledby="newScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newScheduleModalLabel">New Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="newScheduleForm" action="{{ route('admin.scheduling.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="scheduleDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="scheduleDate" name="scheduleDate"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="scheduleStart" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="scheduleStart" name="scheduleStart"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="scheduleEnd" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="scheduleEnd" name="scheduleEnd"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="scheduleStudent" class="form-label">Student</label>
                                <select class="form-select" id="scheduleStudent" name="scheduleStudent" required>
                                    <option value="" selected disabled>Select student</option>
                                    @foreach ($allStudents as $student)
                                        <option value="{{ $student->id }}"
                                            data-stages="{{ json_encode($student->stages) }}">
                                            {{ $student->first_name }}
                                            {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="scheduleStage" class="form-label">Stage</label>
                                <select class="form-select" id="scheduleStage" name="scheduleStage" required
                                    disabled>
                                    <option value="" selected disabled>Select student first</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="scheduleInstructor" class="form-label">Instructor</label>
                                <select class="form-select" id="scheduleInstructor" name="scheduleInstructor"
                                    required>
                                    <option value="" selected disabled>Select instructor</option>
                                    @foreach ($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->first_name }}
                                            {{ $instructor->middle_name ? $instructor->middle_name . ' ' : '' }}{{ $instructor->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="scheduleAircraft" class="form-label">Aircraft</label>
                                <select class="form-select" id="scheduleAircraft" name="scheduleAircraft" required>
                                    <option value="" selected disabled>Select aircraft</option>
                                    @foreach ($aircrafts as $aircraft)
                                        <option value="{{ $aircraft->id }}">{{ $aircraft->registration }}
                                            ({{ $aircraft->model }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label for="lessonType" class="form-label">Lesson Type</label>
                                <input type="text" class="form-control" id="lessonType" name="lessonType"
                                    required placeholder="e.g. Cross-Country Flight, Solo Flying">
                            </div>

                            <div class="col-md-12">
                                <label for="scheduleRoute" class="form-label">Flight Route (Departure & Destination)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt-fill text-primary"></i></span>
                                    <input type="text" class="form-control" id="scheduleRoute" name="scheduleRoute"
                                        placeholder="e.g. MNL to CDO or MNL to CDO - CDO to MNL">
                                </div>
                                <div class="form-text text-muted mt-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle me-1"></i><strong>Format Examples:</strong><br>
                                    • One-Way: <code>MNL to CDO</code><br>
                                    • Roundtrip / Balikan: <code>MNL to CDO - CDO to MNL</code>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="scheduleRemarks" class="form-label">Remarks/Notes</label>
                                <textarea class="form-control" id="scheduleRemarks" name="scheduleRemarks" rows="3"
                                    placeholder="Add scheduling notes or special instructions."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Schedule Modal -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleModalLabel">Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editScheduleForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editScheduleDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="editScheduleDate" name="scheduleDate"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="editScheduleStart" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="editScheduleStart"
                                    name="scheduleStart" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editScheduleEnd" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="editScheduleEnd" name="scheduleEnd"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="editScheduleStudent" class="form-label">Student</label>
                                <select class="form-select" id="editScheduleStudent" name="scheduleStudent" required>
                                    <option value="" disabled>Select student</option>
                                    @foreach ($allStudents as $student)
                                        <option value="{{ $student->id }}"
                                            data-stages="{{ json_encode($student->stages) }}">
                                            {{ $student->first_name }}
                                            {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="editScheduleStage" class="form-label">Stage</label>
                                <select class="form-select" id="editScheduleStage" name="scheduleStage" required
                                    disabled>
                                    <option value="" disabled>Select student first</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="editScheduleInstructor" class="form-label">Instructor</label>
                                <select class="form-select" id="editScheduleInstructor" name="scheduleInstructor"
                                    required>
                                    <option value="" disabled>Select instructor</option>
                                    @foreach ($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->first_name }}
                                            {{ $instructor->middle_name ? $instructor->middle_name . ' ' : '' }}{{ $instructor->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="editScheduleAircraft" class="form-label">Aircraft</label>
                                <select class="form-select" id="editScheduleAircraft" name="scheduleAircraft"
                                    required>
                                    <option value="" disabled>Select aircraft</option>
                                    @foreach ($aircrafts as $aircraft)
                                        <option value="{{ $aircraft->id }}">{{ $aircraft->registration }}
                                            ({{ $aircraft->model }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="editLessonType" class="form-label">Lesson Type</label>
                                <input type="text" class="form-control" id="editLessonType" name="lessonType"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="editScheduleStatus" class="form-label">Status</label>
                                <select class="form-select" id="editScheduleStatus" name="scheduleStatus" required>
                                    <option value="Scheduled">Scheduled</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label for="editScheduleRoute" class="form-label">Flight Route (Departure & Destination)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt-fill text-primary"></i></span>
                                    <input type="text" class="form-control" id="editScheduleRoute" name="scheduleRoute"
                                        placeholder="e.g. MNL to CDO or MNL to CDO - CDO to MNL">
                                </div>
                                <div class="form-text text-muted mt-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle me-1"></i><strong>Format Examples:</strong><br>
                                    • One-Way: <code>MNL to CDO</code><br>
                                    • Roundtrip / Balikan: <code>MNL to CDO - CDO to MNL</code>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="editScheduleRemarks" class="form-label">Remarks/Notes</label>
                                <textarea class="form-control" id="editScheduleRemarks" name="scheduleRemarks" rows="3"
                                    placeholder="Add scheduling notes or special instructions."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Global Delete Form -->
    <form id="globalDeleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        const scheduleTableEl = document.getElementById('scheduleTable');
        let scheduleDataTable;

        if (scheduleTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            scheduleDataTable = window.jQuery(scheduleTableEl).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false,
                columnDefs: [{
                    targets: [5],
                    orderable: false,
                    searchable: false
                }],
            });
        }

        // --- STUDENT SELECTION STAGES POPULATION (ADD MODAL) ---
        $('#scheduleStudent').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            let stages = [];
            try {
                stages = selectedOption.data('stages');
            } catch (e) {}

            const stageSelect = $('#scheduleStage');
            stageSelect.empty();

            if (stages && stages.length > 0) {
                stageSelect.prop('disabled', false);
                stageSelect.append('<option value="" selected disabled>Select stage</option>');
                stages.forEach(stg => {
                    stageSelect.append(`<option value="${stg.id}">${stg.stage} (${stg.status})</option>`);
                });
            } else {
                stageSelect.prop('disabled', true);
                stageSelect.append(
                    '<option value="" selected disabled>No stages configured for this student</option>');
            }
        });

        // --- STUDENT SELECTION STAGES POPULATION (EDIT MODAL) ---
        $('#editScheduleStudent').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            let stages = [];
            try {
                stages = selectedOption.data('stages');
            } catch (e) {}

            const stageSelect = $('#editScheduleStage');
            stageSelect.empty();

            if (stages && stages.length > 0) {
                stageSelect.prop('disabled', false);
                stageSelect.append('<option value="" disabled>Select stage</option>');
                stages.forEach(stg => {
                    stageSelect.append(`<option value="${stg.id}">${stg.stage} (${stg.status})</option>`);
                });
            } else {
                stageSelect.prop('disabled', true);
                stageSelect.append('<option value="" disabled>No stages configured for this student</option>');
            }
        });

        $(document).on('click', '.add-route-preset', function(e) {
            e.preventDefault();
            $('#scheduleRoute').val($(this).data('route'));
        });

        $(document).on('click', '.edit-route-preset', function(e) {
            e.preventDefault();
            $('#editScheduleRoute').val($(this).data('route'));
        });

        // --- EDIT BUTTON HANDLER ---
        $(document).on('click', '.btn-edit-schedule', function() {
            const btn = $(this);
            const id = btn.data('id');

            // Format times (H:i)
            const startTime = btn.data('start').substring(0, 5);
            const endTime = btn.data('end').substring(0, 5);

            $('#editScheduleForm').attr('action', `/admin/scheduling/${id}/update`);
            $('#editScheduleDate').val(btn.data('date'));
            $('#editScheduleStart').val(startTime);
            $('#editScheduleEnd').val(endTime);

            // Set student and trigger change event to populate stages
            const studentId = btn.data('student-id');
            $('#editScheduleStudent').val(studentId).trigger('change');

            // Set stage value
            $('#editScheduleStage').val(btn.data('stage-id'));

            $('#editScheduleInstructor').val(btn.data('instructor-id'));
            $('#editScheduleAircraft').val(btn.data('aircraft-id'));
            $('#editLessonType').val(btn.data('lesson-type'));
            $('#editScheduleRoute').val(btn.data('route') || '');
            $('#editScheduleStatus').val(btn.data('status'));
            $('#editScheduleRemarks').val(btn.data('remarks'));

            $('#editScheduleModal').modal('show');
        });
    </script>

    <!-- Student Breakdown Modal -->
    <div class="modal fade" id="studentBreakdownModal" tabindex="-1" aria-labelledby="studentBreakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="studentBreakdownModalLabel">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>
                        Student Progress & Schedule Breakdown
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded border mb-4">
                        <div>
                            <span class="text-muted small uppercase fw-semibold">Student Name</span>
                            <h5 class="fw-bold mb-0 text-primary" id="modalStudentName">-</h5>
                        </div>
                        <div>
                            <span class="text-muted small uppercase fw-semibold">Total Schedules</span>
                            <h6 class="fw-bold mb-0 text-dark" id="modalTotalSchedules">0 Flight Sessions</h6>
                        </div>
                    </div>

                    <div id="modalStagesContainer" class="vstack gap-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- STUDENT BREAKDOWN MODAL HANDLER ---
        $(document).on('click', '.btn-view-student-breakdown', function() {
            const btn = $(this);
            const studentName = btn.data('student-name');
            const totalSchedules = btn.data('schedules-count');
            let breakdown = [];
            try {
                breakdown = btn.data('breakdown') || [];
            } catch (e) {
                breakdown = [];
            }

            $('#modalStudentName').text(studentName);
            $('#modalTotalSchedules').text(`${totalSchedules} Scheduled Session(s)`);

            const container = $('#modalStagesContainer');
            container.empty();

            if (Array.isArray(breakdown) && breakdown.length > 0) {
                breakdown.forEach((stg) => {
                    const stgStatus = stg.status || 'In progress';
                    const stgStatusClass = stgStatus === 'Completed' ? 'bg-success' : 'bg-warning text-dark';

                    let lessonsRowsHtml = '';
                    if (stg.lessons && stg.lessons.length > 0) {
                        stg.lessons.forEach(lsn => {
                            let badgeHtml = '<span class="badge bg-secondary">Pending</span>';
                            if (lsn.status === 'Completed') {
                                badgeHtml = '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Completed</span>';
                            } else if (lsn.status === 'Scheduled') {
                                badgeHtml = '<span class="badge bg-primary"><i class="bi bi-calendar-event me-1"></i>Scheduled</span>';
                            } else if (lsn.status) {
                                badgeHtml = `<span class="badge bg-info text-dark">${lsn.status}</span>`;
                            }

                            const routeBadge = lsn.route ? `<span class="badge bg-light text-primary border ms-1"><i class="bi bi-geo-alt me-1"></i>${lsn.route}</span>` : '';
                            const schedDetails = lsn.date ? `${lsn.date} (${lsn.time || ''}) - ${lsn.instructor || 'N/A'} [${lsn.aircraft || 'N/A'}] ${routeBadge}` : '<span class="text-muted fs-7">Not scheduled yet</span>';

                            lessonsRowsHtml += `
                                <tr>
                                    <td class="fw-semibold text-dark">${lsn.lesson_name}</td>
                                    <td class="text-center">${badgeHtml}</td>
                                    <td class="small text-secondary">${schedDetails}</td>
                                </tr>
                            `;
                        });
                    } else {
                        lessonsRowsHtml = '<tr><td colspan="3" class="text-center text-muted py-3"><i class="bi bi-info-circle me-1"></i> No scheduled or graded lessons for this stage yet.</td></tr>';
                    }

                    const cardHtml = `
                        <div class="card border shadow-sm rounded-3">
                            <div class="card-header bg-white d-flex align-items-center justify-content-between py-2">
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-diagram-3-fill text-primary me-2"></i> ${stg.stage}
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark border me-2"><i class="bi bi-clock me-1"></i>Req: ${stg.required_hours} hrs</span>
                                    <span class="badge ${stgStatusClass}">${stgStatus}</span>
                                </div>
                            </div>
                            <div class="card-body p-0" style="overflow-x:auto;">
                                <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Lesson</th>
                                            <th class="text-center" style="width: 120px;">Status</th>
                                            <th>Schedule Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${lessonsRowsHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                    container.append(cardHtml);
                });
            } else {
                container.append('<div class="alert alert-secondary text-center">No stage or schedule details found.</div>');
            }

            $('#studentBreakdownModal').modal('show');
        });
    </script>
</body>

</html>
