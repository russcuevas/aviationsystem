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
                            <th>Date</th>
                            <th>Student</th>
                            <th>Stage</th>
                            <th>Lesson Type</th>
                            <th>Instructor</th>
                            <th>Aircraft</th>
                            <th>Time Slot</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr data-id="{{ $schedule->id }}">
                                <td data-order="{{ $schedule->date }}">{{ date('M j, Y', strtotime($schedule->date)) }}
                                </td>
                                <td>{{ $schedule->student_name }}</td>
                                <td>
                                    <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"
                                        style="font-size: 0.72rem;">
                                        {{ $schedule->stage_name }}
                                    </span>
                                </td>
                                <td>{{ $schedule->lesson_type }}</td>

                                <td>{{ $schedule->instructor_name }}</td>
                                <td>
                                    <span class="school-code">{{ $schedule->aircraft_reg }}</span>
                                </td>
                                <td>{{ date('h:i A', strtotime($schedule->start_time)) }} <br> to <br>
                                    {{ date('h:i A', strtotime($schedule->end_time)) }}</td>
                                <td>
                                    <span
                                        class="school-status status-{{ strtolower($schedule->status) }}">{{ $schedule->status }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-warning btn-edit-schedule"
                                            data-id="{{ $schedule->id }}" data-date="{{ $schedule->date }}"
                                            data-start="{{ $schedule->start_time }}"
                                            data-end="{{ $schedule->end_time }}"
                                            data-student-id="{{ $schedule->student_id }}"
                                            data-stage-id="{{ $schedule->stage_id }}"
                                            data-instructor-id="{{ $schedule->instructor_id }}"
                                            data-aircraft-id="{{ $schedule->aircraft_id }}"
                                            data-lesson-type="{{ $schedule->lesson_type }}"
                                            data-status="{{ $schedule->status }}"
                                            data-remarks="{{ $schedule->remarks }}" title="Edit"><i
                                                class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-schedule"
                                            data-id="{{ $schedule->id }}" title="Delete"><i
                                                class="bi bi-trash"></i></button>
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
                                    @foreach ($students as $student)
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
                                    @foreach ($students as $student)
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
                    targets: [8],
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
            $('#editScheduleStatus').val(btn.data('status'));
            $('#editScheduleRemarks').val(btn.data('remarks'));

            $('#editScheduleModal').modal('show');
        });

        // --- DELETE BUTTON HANDLER ---
        $(document).on('click', '.btn-delete-schedule', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
                const form = document.getElementById('globalDeleteForm');
                form.action = `/admin/scheduling/${id}`;
                form.submit();
            }
        });
    </script>
</body>

</html>
