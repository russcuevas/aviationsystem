<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor - Flight Hours Encoding</title>
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
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (isset($providerName))
            <span class="badge bg-primary px-3 py-2 mb-3"
                style="font-size: 0.9rem; font-weight: 600; border-radius: 8px; background-color: var(--cobalt) !important;">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ $providerName }}
            </span>
        @endif

        <div class="page-header">
            <h2>Flight Hours Encoding</h2>
            <p>Input actual flight hours for each student session.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Flight Hours Encoding</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Encoded Flight Hours</p>
                    <p class="panel-subtitle">Recent sessions encoded for validation and records.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#logHoursModal"><i
                        class="bi bi-plus-lg"></i> Log Hours</button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorHoursTable">
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Aircraft</th>
                            <th>Training Stage</th>
                            <th>Lesson</th>
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
                            <tr>
                                <td><span class="fw-semibold text-primary">{{ $hour->log_id }}</span></td>
                                <td
                                    data-order="{{ $hour->date ? $hour->date->format('Y-m-d') : ($hour->created_at ? $hour->created_at->format('Y-m-d') : '') }}">
                                    {{ $hour->date ? $hour->date->format('M d, Y') : ($hour->created_at ? $hour->created_at->format('M d, Y') : '-') }}
                                </td>
                                <td>{{ $hour->student ? $hour->student->first_name . ' ' . $hour->student->last_name : 'N/A' }}
                                </td>
                                <td>{{ $hour->instructor ? $hour->instructor->first_name . ' ' . $hour->instructor->last_name : 'N/A' }}
                                </td>
                                <td>{{ $hour->aircraft ? $hour->aircraft->registration : 'N/A' }}</td>
                                <td><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $hour->stage ? $hour->stage->stage : 'N/A' }}</span>
                                </td>
                                <td>{{ $hour->lesson ?? 'N/A' }}</td>
                                <td>{{ $hour->dual_instruction_time !== null ? number_format($hour->dual_instruction_time, 1) . ' hrs' : '-' }}
                                </td>
                                <td>{{ $hour->pic_time !== null ? number_format($hour->pic_time, 1) . ' hrs' : '-' }}
                                </td>
                                <td>{{ $hour->solo_time !== null ? number_format($hour->solo_time, 1) . ' hrs' : '-' }}
                                </td>
                                <td>{{ $hour->instrument_flight_time !== null ? number_format($hour->instrument_flight_time, 1) . ' hrs' : '-' }}
                                </td>
                                <td><strong>{{ number_format($hour->total_time, 1) }} hrs</strong></td>
                                <td>
                                    @if ($hour->status === 'pending review')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i
                                                class="bi bi-clock-history me-1"></i>pending review</span>
                                    @elseif ($hour->status === 'confirmed' || $hour->status === 'approved')
                                        <span class="badge bg-success px-2 py-1"><i
                                                class="bi bi-check-circle me-1"></i>confirmed</span>
                                    @elseif ($hour->status === 'cancelled')
                                        <span class="badge bg-danger px-2 py-1"><i
                                                class="bi bi-x-circle me-1"></i>cancelled</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">{{ $hour->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $hour->remarks ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="logHoursModal" tabindex="-1" aria-labelledby="logHoursModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logHoursModalLabel">Log Flight Hours</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="logHoursForm" action="{{ route('instructor.flight.hours.encoding.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="date" class="form-label fw-medium">Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-4">
                                <label for="student_id" class="form-label fw-medium">Student <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="" selected disabled>Select student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            data-stages="{{ json_encode($student->stages) }}">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="aircraft_id" class="form-label fw-medium">Aircraft <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="aircraft_id" name="aircraft_id" required>
                                    <option value="" selected disabled>Select aircraft</option>
                                    @foreach ($aircrafts as $aircraft)
                                        <option value="{{ $aircraft->id }}">{{ $aircraft->registration }}
                                            ({{ $aircraft->type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="stage_id" class="form-label fw-medium">Training Stage</label>
                                <select class="form-select" id="stage_id" name="stage_id" disabled>
                                    <option value="" selected disabled>Select student first</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="lesson_select" class="form-label fw-medium">Lesson</label>
                                <select class="form-select" id="lesson_select" name="lesson" disabled>
                                    <option value="" selected disabled>Select training stage first</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none" id="custom_lesson_input"
                                    placeholder="Enter custom lesson name">
                            </div>

                            <div class="col-md-3">
                                <label for="dual_instruction_time" class="form-label fw-medium">Dual Instruction
                                    (hrs)</label>
                                <input type="number" class="form-control hour-input" id="dual_instruction_time"
                                    name="dual_instruction_time" min="0" step="0.1" placeholder="0.0">
                            </div>

                            <div class="col-md-3">
                                <label for="pic_time" class="form-label fw-medium">PIC Time (hrs)</label>
                                <input type="number" class="form-control hour-input" id="pic_time" name="pic_time"
                                    min="0" step="0.1" placeholder="0.0">
                            </div>

                            <div class="col-md-3">
                                <label for="solo_time" class="form-label fw-medium">Solo Time (hrs)</label>
                                <input type="number" class="form-control hour-input" id="solo_time"
                                    name="solo_time" min="0" step="0.1" placeholder="0.0">
                            </div>

                            <div class="col-md-3">
                                <label for="instrument_flight_time" class="form-label fw-medium">Instrument Flight
                                    (hrs)</label>
                                <input type="number" class="form-control hour-input" id="instrument_flight_time"
                                    name="instrument_flight_time" min="0" step="0.1" placeholder="0.0">
                            </div>

                            <div class="col-12">
                                <label for="remarks" class="form-label fw-medium">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"
                                    placeholder="Add notes or details about the last session."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Hours</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        const instructorHoursTableEl = document.getElementById('instructorHoursTable');
        if (instructorHoursTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(instructorHoursTableEl).DataTable({
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
                autoWidth: false
            });
        }

        let currentStudentStages = [];

        // Dynamic Stage dropdown based on Student selection
        $('#student_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            try {
                currentStudentStages = selectedOption.data('stages') || [];
            } catch (e) {
                currentStudentStages = [];
            }

            const stageSelect = $('#stage_id');
            const lessonSelect = $('#lesson_select');
            const customInput = $('#custom_lesson_input');

            stageSelect.empty();
            lessonSelect.empty().prop('disabled', true).append(
                '<option value="" selected disabled>Select training stage first</option>');
            customInput.addClass('d-none').val('');

            if (currentStudentStages && currentStudentStages.length > 0) {
                stageSelect.prop('disabled', false);
                stageSelect.append('<option value="" selected disabled>Select stage</option>');
                currentStudentStages.forEach(stg => {
                    stageSelect.append(`<option value="${stg.id}">${stg.stage} (${stg.status})</option>`);
                });
            } else {
                stageSelect.prop('disabled', true);
                stageSelect.append('<option value="" selected disabled>No stages found for this student</option>');
            }
        });

        // Dynamic Lesson dropdown based on Stage selection (loads ONLY scheduled lessons for selected student & stage)
        $('#stage_id').on('change', function() {
            const selectedStageId = $(this).val();
            const lessonSelect = $('#lesson_select');
            const customInput = $('#custom_lesson_input');

            lessonSelect.empty();
            customInput.addClass('d-none').val('');

            const selectedStageObj = currentStudentStages.find(s => s.id == selectedStageId);
            let lessons = selectedStageObj && selectedStageObj.lessons ? selectedStageObj.lessons : [];

            if (lessons && lessons.length > 0) {
                lessonSelect.prop('disabled', false);
                lessonSelect.append('<option value="" selected disabled>Select scheduled lesson</option>');
                lessons.forEach(lsn => {
                    lessonSelect.append(`<option value="${lsn}">${lsn}</option>`);
                });
                lessonSelect.append('<option value="__custom__">+ Enter Custom Lesson</option>');
            } else {
                lessonSelect.prop('disabled', false);
                lessonSelect.append(
                    '<option value="" selected disabled>No scheduled lessons for this stage</option>');
                lessonSelect.append('<option value="__custom__">+ Enter Custom Lesson</option>');
            }
        });

        // Handle Custom Lesson toggle
        $('#lesson_select').on('change', function() {
            const val = $(this).val();
            const customInput = $('#custom_lesson_input');
            if (val === '__custom__') {
                customInput.removeClass('d-none').prop('required', true).focus();
                $(this).removeAttr('name');
                customInput.attr('name', 'lesson');
            } else {
                customInput.addClass('d-none').prop('required', false).val('');
                customInput.removeAttr('name');
                $(this).attr('name', 'lesson');
            }
        });
    </script>
</body>

</html>
