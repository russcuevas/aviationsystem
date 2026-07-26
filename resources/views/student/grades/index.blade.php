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
            <h2>Grade Viewing</h2>
            <p>View your evaluation results and finalized performance records.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Grade Viewing</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">My Grade Sheets</p>
                    <p class="panel-subtitle">Instructor evaluations and school-reviewed results.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="studentGradesTable">
                    <thead>
                        <tr>
                            <th>Sheet ID</th>
                            <th>Date</th>
                            <th>Stage</th>
                            <th>Evaluated Lessons</th>
                            <th>Instructor</th>
                            <th>Result</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gradeSheets as $sheet)
                            @php
                                $uniqueStages = [];
                                $lessonNames = [];
                                if (is_array($sheet->lesson_grades)) {
                                    foreach ($sheet->lesson_grades as $lg) {
                                        if (!empty($lg['stage'])) {
                                            $uniqueStages[] = $lg['stage'];
                                        }
                                        if (!empty($lg['lesson'])) {
                                            $lessonNames[] = $lg['lesson'];
                                        }
                                    }
                                }
                                $uniqueStages = array_unique($uniqueStages);
                                $stageDisplay = $sheet->stage ? $sheet->stage->stage : (count($uniqueStages) > 0 ? implode(', ', $uniqueStages) : 'All Stages');

                                $g = $sheet->overall_grade;
                                $badgeClass = 'bg-secondary';
                                if (in_array($g, ['A+', 'A'])) $badgeClass = 'bg-success';
                                elseif (in_array($g, ['B+', 'B'])) $badgeClass = 'bg-primary';
                                elseif (in_array($g, ['C+', 'C'])) $badgeClass = 'bg-warning text-dark';
                                elseif ($g === 'F') $badgeClass = 'bg-danger';
                            @endphp
                            <tr>
                                <td><span class="fw-semibold text-primary">{{ $sheet->sheet_id }}</span></td>
                                <td data-order="{{ $sheet->date ? $sheet->date->format('Y-m-d') : '' }}">
                                    {{ $sheet->date ? $sheet->date->format('M j, Y') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $stageDisplay }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border">{{ count($lessonNames) }} Lessons</span>
                                </td>
                                <td>{{ $sheet->instructor ? $sheet->instructor->first_name . ' ' . $sheet->instructor->last_name : 'N/A' }}</td>
                                <td>
                                    @if ($sheet->total_score >= 75)
                                        <span class="school-status status-active"><i class="bi bi-check-circle-fill me-1"></i>Passed</span>
                                    @else
                                        <span class="school-status status-inactive"><i class="bi bi-x-circle-fill me-1"></i>Failed</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sheet->status === 'Accepted' || $sheet->status === 'Approved' || $sheet->status === 'Finalized')
                                        <span class="school-status status-active"><i class="bi bi-check-circle me-1"></i>Finalized</span>
                                    @else
                                        <span class="school-status status-inactive"><i class="bi bi-clock-history me-1"></i>Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-view-grade-breakdown" type="button"
                                        data-sheetid="{{ $sheet->sheet_id }}"
                                        data-date="{{ $sheet->date ? $sheet->date->format('M j, Y') : '-' }}"
                                        data-instructor="{{ $sheet->instructor ? $sheet->instructor->first_name . ' ' . $sheet->instructor->last_name : 'N/A' }}"
                                        data-stage="{{ $stageDisplay }}"
                                        data-avg="{{ number_format($sheet->total_score, 1) }}"
                                        data-lessons="{{ json_encode($sheet->lesson_grades) }}"
                                        data-remarks="{{ $sheet->remarks }}">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Student Grade Sheet Breakdown Modal -->
    <div class="modal fade" id="studentGradeModal" tabindex="-1" aria-labelledby="studentGradeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="studentGradeModalLabel"><i class="bi bi-journal-check text-primary me-2"></i>Grade Evaluation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border flex-wrap gap-2">
                        <div>
                            <div class="text-muted small">Sheet ID</div>
                            <div class="fw-bold text-primary" id="sgSheetId">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Date</div>
                            <div class="fw-semibold text-dark" id="sgDate">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Instructor</div>
                            <div class="fw-semibold text-dark" id="sgInstructor">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Stage</div>
                            <div class="fw-semibold text-dark" id="sgStage">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Overall Result</div>
                            <div class="fw-bold fs-5 text-dark" id="sgPassFailBadge">-</div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-list-check text-primary me-1"></i> Evaluated Lessons</h6>
                    <div class="table-responsive mb-3 border rounded">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Stage</th>
                                    <th>Lesson</th>
                                    <th class="text-center">Result</th>
                                </tr>
                            </thead>
                            <tbody id="sgLessonsTableBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-chat-left-text me-1 text-primary"></i> Instructor Remarks</div>
                        <p class="mb-0 text-secondary" id="sgRemarks">No remarks provided.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($('#studentGradesTable').length && $.fn.DataTable) {
                $('#studentGradesTable').DataTable({
                    pageLength: 10,
                    order: [
                        [1, 'desc']
                    ],
                    autoWidth: false,
                    destroy: true,
                    language: {
                        emptyTable: "No grade sheets found."
                    }
                });
            }

            $(document).on('click', '.btn-view-grade-breakdown', function() {
                const btn = $(this);
                $('#sgSheetId').text(btn.data('sheetid'));
                $('#sgDate').text(btn.data('date'));
                $('#sgInstructor').text(btn.data('instructor'));
                $('#sgStage').text(btn.data('stage'));
                
                const avgScore = parseFloat(btn.data('avg')) || 0;
                if (avgScore >= 75) {
                    $('#sgPassFailBadge').html('<span class="school-status status-active"><i class="bi bi-check-circle-fill me-1"></i>Passed</span>');
                } else {
                    $('#sgPassFailBadge').html('<span class="school-status status-inactive"><i class="bi bi-x-circle-fill me-1"></i>Failed</span>');
                }

                let remarks = btn.data('remarks') || 'No remarks provided.';
                if (remarks.trim().toLowerCase() === 'none') remarks = 'N/A';
                $('#sgRemarks').text(remarks);

                let lessons = [];
                try {
                    lessons = btn.data('lessons') || [];
                } catch(e) {
                    lessons = [];
                }

                const tbody = $('#sgLessonsTableBody');
                tbody.empty();

                if (Array.isArray(lessons) && lessons.length > 0) {
                    lessons.forEach((lsn, idx) => {
                        const lsnScore = lsn.score !== undefined ? parseFloat(lsn.score) : 0;
                        const pfBadge = lsnScore >= 75
                            ? '<span class="school-status status-active" style="font-size:0.75rem;"><i class="bi bi-check-circle-fill me-1"></i>Passed</span>'
                            : '<span class="school-status status-inactive" style="font-size:0.75rem;"><i class="bi bi-x-circle-fill me-1"></i>Failed</span>';

                        tbody.append(`
                            <tr>
                                <td class="fw-bold text-muted">${idx + 1}</td>
                                <td><span class="badge bg-light text-dark border">${lsn.stage || btn.data('stage')}</span></td>
                                <td class="fw-semibold text-dark">${lsn.lesson || '-'}</td>
                                <td class="text-center">${pfBadge}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center text-muted py-3">No lesson grades found.</td></tr>');
                }

                $('#studentGradeModal').modal('show');
            });
        });
    </script>
</body>

</html>
