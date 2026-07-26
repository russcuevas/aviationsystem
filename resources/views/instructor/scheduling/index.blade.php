<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor - Schedule & Progress</title>
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
            <h2>Schedule & Student Progress</h2>
            <p>View assigned flight schedules and track lesson-by-lesson progress for your students.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Schedule & Progress</span></div>
        </div>

        <div class="panel mb-3">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Assigned Student Flight Board</p>
                    <p class="panel-subtitle">Overview of your assigned student pilots and progress breakdown.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorScheduleTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Assigned Stages</th>
                            <th>Total Flight Schedules</th>
                            <th>Overall Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @php
                                $totalStages = count($student->stages_breakdown ?? []);
                                $completedStages = 0;
                                foreach ($student->stages_breakdown ?? [] as $stgItem) {
                                    if (($stgItem['status'] ?? '') === 'Completed') {
                                        $completedStages++;
                                    }
                                }
                            @endphp
                            <tr>
                                <td><span class="school-code">STU-{{ sprintf('%03d', $student->id) }}</span></td>
                                <td class="fw-bold text-dark">{{ $student->first_name }} {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}</td>
                                <td>
                                    @forelse($student->stages_breakdown ?? [] as $stgItem)
                                        <span class="badge bg-light text-dark border me-1">{{ $stgItem['stage'] }}</span>
                                    @empty
                                        <span class="text-muted small">No stage assigned</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1" style="font-size:0.8rem;">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $student->schedules_count ?? 0 }} Flights
                                    </span>
                                </td>
                                <td>
                                    @if($totalStages > 0 && $completedStages === $totalStages)
                                        <span class="school-status status-active"><i class="bi bi-check-circle-fill me-1"></i>Completed</span>
                                    @elseif($completedStages > 0)
                                        <span class="school-status status-onleave"><i class="bi bi-hourglass-split me-1"></i>In Progress ({{ $completedStages }}/{{ $totalStages }})</span>
                                    @else
                                        <span class="school-status status-onleave"><i class="bi bi-clock me-1"></i>In Progress (0/{{ $totalStages }})</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-view-instructor-breakdown"
                                        data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                                        data-schedules-count="{{ $student->schedules_count ?? 0 }}"
                                        data-breakdown='@json($student->stages_breakdown ?? [])'>
                                        <i class="bi bi-eye me-1"></i> View Breakdown
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Instructor Student Breakdown Modal -->
    <div class="modal fade" id="instructorStudentBreakdownModal" tabindex="-1" aria-labelledby="instructorStudentBreakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="instructorStudentBreakdownModalLabel">
                        <i class="bi bi-journal-check text-primary me-2"></i>
                        Student Stage & Lesson Breakdown
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded border mb-4">
                        <div>
                            <span class="text-muted small uppercase fw-semibold">Student Name</span>
                            <h5 class="fw-bold mb-0 text-primary" id="instModalStudentName">-</h5>
                        </div>
                        <div>
                            <span class="text-muted small uppercase fw-semibold">Flight Sessions</span>
                            <h6 class="fw-bold mb-0 text-dark" id="instModalTotalSchedules">0 Sessions</h6>
                        </div>
                    </div>

                    <div id="instModalStagesContainer" class="vstack gap-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        const scheduleTableEl = document.getElementById('instructorScheduleTable');
        if (scheduleTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(scheduleTableEl).DataTable({
                pageLength: 10,
                order: [
                    [1, 'asc']
                ],
                autoWidth: false,
                columnDefs: [{
                    targets: [5],
                    orderable: false,
                    searchable: false
                }]
            });
        }

        // --- INSTRUCTOR STUDENT BREAKDOWN MODAL HANDLER ---
        $(document).on('click', '.btn-view-instructor-breakdown', function() {
            const btn = $(this);
            const studentName = btn.data('student-name');
            const totalSchedules = btn.data('schedules-count');
            let breakdown = [];
            try {
                breakdown = btn.data('breakdown') || [];
            } catch (e) {
                breakdown = [];
            }

            $('#instModalStudentName').text(studentName);
            $('#instModalTotalSchedules').text(`${totalSchedules} Flight Session(s)`);

            const container = $('#instModalStagesContainer');
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

                            const schedDetails = lsn.date ? `${lsn.date} (${lsn.time || ''}) [${lsn.aircraft || 'N/A'}]` : '<span class="text-muted fs-7">Not scheduled yet</span>';

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
                                            <th>Schedule & Flight Details</th>
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
                container.append('<div class="alert alert-secondary text-center">No stage or lesson breakdown available for this student.</div>');
            }

            $('#instructorStudentBreakdownModal').modal('show');
        });
    </script>
</body>

</html>
