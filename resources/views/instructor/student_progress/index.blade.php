<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor - Student Progress</title>
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
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
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
            <h2>Student Progress Update</h2>
            <p>Mark lesson completion and add instructor remarks after each session.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Student Progress Update</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Recent Progress Updates</p>
                    <p class="panel-subtitle">Latest lesson completion logs and remarks.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorProgressTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Stage</th>
                            <th>Lesson</th>
                            <th>Hours</th>
                            <th>Completion</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $sched)
                            <tr>
                                <td data-order="{{ $sched->date }}">
                                    {{ \Carbon\Carbon::parse($sched->date)->format('M d, Y') }}
                                </td>
                                <td>{{ $sched->student_name }}</td>
                                <td><span class="badge bg-light text-dark border px-2 py-1">{{ $sched->stage_name }}</span></td>
                                <td>{{ $sched->lesson_type }}</td>
                                <td>{{ $sched->calculated_hours ? number_format($sched->calculated_hours, 1) . ' hrs' : '-' }}</td>
                                <td>
                                    @if ($sched->status === 'Completed')
                                        <span class="school-status status-active">Completed</span>
                                    @elseif ($sched->status === 'Scheduled' || $sched->status === 'In Progress')
                                        <span class="school-status status-onleave">In Progress</span>
                                    @else
                                        <span class="school-status status-inactive">{{ $sched->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $sched->remarks ?? '-' }}</td>
                                <td>
                                    <button class="btn-review btn-edit-progress" type="button"
                                        data-id="{{ $sched->id }}"
                                        data-student="{{ $sched->student_name }}"
                                        data-stage="{{ $sched->stage_name }}"
                                        data-lesson="{{ $sched->lesson_type }}"
                                        data-status="{{ $sched->status }}"
                                        data-remarks="{{ $sched->remarks }}">
                                        <i class="bi bi-pencil-square me-1"></i> Edit Progress
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Edit Progress Modal -->
    <div class="modal fade" id="editProgressModal" tabindex="-1" aria-labelledby="editProgressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProgressModalLabel">Edit Student Progress</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editProgressForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted mb-1 small text-uppercase fw-bold">Student</label>
                            <div class="fw-semibold text-dark fs-6" id="modalStudentName">-</div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted mb-1 small text-uppercase fw-bold">Stage</label>
                                <div><span class="badge bg-light text-dark border px-2 py-1" id="modalStageName">-</span></div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted mb-1 small text-uppercase fw-bold">Lesson</label>
                                <div class="fw-medium text-dark" id="modalLessonType">-</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="progressStatus" class="form-label fw-medium">Completion Status</label>
                            <select class="form-select" id="progressStatus" name="status" required>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="progressRemarks" class="form-label fw-medium">Instructor Remarks</label>
                            <textarea class="form-control" id="progressRemarks" name="remarks" rows="3"
                                placeholder="Add notes on performance, feedback, or items to improve."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Progress</button>
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
        const instructorProgressTableEl = document.getElementById('instructorProgressTable');
        if (instructorProgressTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(instructorProgressTableEl).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false
            });
        }

        $(document).on('click', '.btn-edit-progress', function() {
            const btn = $(this);
            const id = btn.data('id');
            const student = btn.data('student');
            const stage = btn.data('stage');
            const lesson = btn.data('lesson');
            let status = btn.data('status');
            const remarks = btn.data('remarks');

            if (status === 'Scheduled') {
                status = 'In Progress';
            }

            $('#editProgressForm').attr('action', `/instructor/student-progress/${id}/update`);
            $('#modalStudentName').text(student);
            $('#modalStageName').text(stage);
            $('#modalLessonType').text(lesson);
            $('#progressStatus').val(status);
            $('#progressRemarks').val(remarks || '');

            $('#editProgressModal').modal('show');
        });
    </script>
</body>

</html>
