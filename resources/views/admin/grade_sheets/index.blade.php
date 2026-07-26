<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Admin - Grade Sheet Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
    @include('admin.components.left_sidebar')

    @include('admin.components.topbar')

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
            <h2>Grade Sheet Management</h2>
            <p>Review submitted grades and finalize student evaluations.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Grade Sheets</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Instructor Grade Submissions</p>
                    <p class="panel-subtitle">Finalize results after review.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="gradeAdminTable">
                    <thead>
                        <tr>
                            <th>Sheet ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Stage</th>
                            <th>Evaluated Lessons</th>
                            <th>Average Score</th>
                            <th>Overall Grade</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gradeSheets as $sheet)
                            <tr>
                                <td><span class="fw-semibold text-primary">{{ $sheet->sheet_id }}</span></td>
                                <td data-order="{{ $sheet->date ? $sheet->date->format('Y-m-d') : '' }}">
                                    {{ $sheet->date ? $sheet->date->format('M d, Y') : '-' }}
                                </td>
                                <td class="fw-semibold">
                                    {{ $sheet->student ? $sheet->student->first_name . ' ' . $sheet->student->last_name : 'N/A' }}
                                </td>
                                <td>
                                    {{ $sheet->instructor ? $sheet->instructor->first_name . ' ' . $sheet->instructor->last_name : 'N/A' }}
                                </td>
                                <td>
                                    @if ($sheet->stage)
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $sheet->stage->stage }}</span>
                                    @else
                                        @php
                                            $uniqueStages = [];
                                            if (is_array($sheet->lesson_grades)) {
                                                foreach ($sheet->lesson_grades as $lg) {
                                                    if (!empty($lg['stage'])) {
                                                        $uniqueStages[] = $lg['stage'];
                                                    }
                                                }
                                            }
                                            $uniqueStages = array_unique($uniqueStages);
                                            $stageDisplay = count($uniqueStages) > 0 ? implode(', ', $uniqueStages) : 'All Stages (Combined)';
                                        @endphp
                                        <span class="badge bg-info text-dark px-2 py-1"><i class="bi bi-layers me-1"></i>{{ $stageDisplay }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php $count = is_array($sheet->lesson_grades) ? count($sheet->lesson_grades) : 0; @endphp
                                    <span class="badge bg-light text-secondary border">{{ $count }} Lessons</span>
                                </td>
                                <td><strong>{{ number_format($sheet->total_score, 1) }} / 100</strong></td>
                                <td>
                                    @php
                                        $g = $sheet->overall_grade;
                                        $badgeClass = 'bg-secondary';
                                        if (in_array($g, ['A+', 'A'])) $badgeClass = 'bg-success';
                                        elseif (in_array($g, ['B+', 'B'])) $badgeClass = 'bg-primary';
                                        elseif (in_array($g, ['C+', 'C'])) $badgeClass = 'bg-warning text-dark';
                                        elseif ($g === 'F') $badgeClass = 'bg-danger';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1">{{ $g }}</span>
                                </td>
                                <td>
                                    @if ($sheet->status === 'Accepted' || $sheet->status === 'Approved' || $sheet->status === 'Finalized')
                                        <span class="school-status status-active"><i class="bi bi-check-circle me-1"></i>Finalized</span>
                                    @elseif ($sheet->status === 'For Review' || $sheet->status === 'Pending')
                                        <span class="school-status status-inactive"><i class="bi bi-clock-history me-1"></i>Pending</span>
                                    @elseif ($sheet->status === 'Rejected')
                                        <span class="school-status status-inactive"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                    @else
                                        <span class="school-status status-inactive">{{ $sheet->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        @php
                                            $stgDisp = $sheet->stage ? $sheet->stage->stage : (count($uniqueStages ?? []) > 0 ? implode(', ', $uniqueStages) : 'All Stages (Combined)');
                                        @endphp
                                        <button class="btn-review btn-view-breakdown" type="button"
                                            data-sheetid="{{ $sheet->sheet_id }}"
                                            data-student="{{ $sheet->student ? $sheet->student->first_name . ' ' . $sheet->student->last_name : 'N/A' }}"
                                            data-instructor="{{ $sheet->instructor ? $sheet->instructor->first_name . ' ' . $sheet->instructor->last_name : 'N/A' }}"
                                            data-stage="{{ $stgDisp }}"
                                            data-avg="{{ number_format($sheet->total_score, 1) }}"
                                            data-grade="{{ $sheet->overall_grade }}"
                                            data-badge="{{ $badgeClass }}"
                                            data-lessons="{{ json_encode($sheet->lesson_grades) }}"
                                            data-remarks="{{ $sheet->remarks }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>

                                        @if ($sheet->status !== 'Accepted')
                                            <form action="{{ route('admin.grade.sheets.status.update', $sheet->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Accepted">
                                                <button type="submit" class="btn btn-sm btn-success py-1 px-2 text-nowrap" style="font-size: 0.75rem;">
                                                    <i class="bi bi-check-lg me-1"></i>Accept
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.grade.sheets.status.update', $sheet->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 text-nowrap" style="font-size: 0.75rem;" onclick="return confirm('Are you sure you want to reject and delete this grade sheet from the database?')">
                                                    <i class="bi bi-trash me-1"></i>Reject
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- View Breakdown Modal -->
    <div class="modal fade" id="viewBreakdownModal" tabindex="-1" aria-labelledby="viewBreakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="viewBreakdownModalLabel"><i class="bi bi-file-text text-primary me-2"></i>Grade Sheet Review & Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border flex-wrap gap-2">
                        <div>
                            <div class="text-muted small">Sheet ID</div>
                            <div class="fw-bold text-primary" id="bdSheetId">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Student</div>
                            <div class="fw-semibold text-dark" id="bdStudentName">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Instructor</div>
                            <div class="fw-semibold text-dark" id="bdInstructorName">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Stage</div>
                            <div class="fw-semibold text-dark" id="bdStageName">-</div>
                        </div>
                        <div>
                            <div class="text-muted small">Overall Score & Grade</div>
                            <div class="fw-bold fs-5 text-dark"><span id="bdAvgScore">0.0</span> / 100 <span class="badge ms-1" id="bdOverallBadge">N/A</span></div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2">Lesson Scores & Time Out Breakdown</h6>
                    <div style="overflow-x:auto;">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Stage</th>
                                    <th>Lesson</th>
                                    <th class="text-center">Time Out</th>
                                    <th class="text-center" style="width: 120px;">Score (1-100)</th>
                                    <th class="text-center" style="width: 100px;">Grade</th>
                                </tr>
                            </thead>
                            <tbody id="bdLessonsTableBody"></tbody>
                        </table>
                    </div>

                    <div class="mt-3" id="bdRemarksContainer">
                        <label class="fw-semibold text-muted small uppercase">Remarks</label>
                        <div class="p-2 border bg-light rounded text-dark" id="bdRemarksText">-</div>
                    </div>
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
        const gradeAdminTableEl = document.getElementById('gradeAdminTable');
        if (gradeAdminTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(gradeAdminTableEl).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false
            });
        }

        // Helper: Convert score to letter grade
        function getLetterGrade(score) {
            if (isNaN(score) || score === '' || score === null) return 'N/A';
            score = parseFloat(score);
            if (score >= 95) return 'A+';
            if (score >= 90) return 'A';
            if (score >= 85) return 'B+';
            if (score >= 80) return 'B';
            if (score >= 75) return 'C+';
            if (score >= 70) return 'C';
            return 'F';
        }

        // Helper: Get badge class
        function getGradeBadgeClass(grade) {
            if (grade === 'A+' || grade === 'A') return 'bg-success';
            if (grade === 'B+' || grade === 'B') return 'bg-primary';
            if (grade === 'C+' || grade === 'C') return 'bg-warning text-dark';
            if (grade === 'F') return 'bg-danger';
            return 'bg-secondary';
        }

        // View Breakdown Modal
        $(document).on('click', '.btn-view-breakdown', function() {
            const btn = $(this);
            $('#bdSheetId').text(btn.data('sheetid'));
            $('#bdStudentName').text(btn.data('student'));
            $('#bdInstructorName').text(btn.data('instructor'));
            $('#bdStageName').text(btn.data('stage'));
            $('#bdAvgScore').text(btn.data('avg'));
            
            const grade = btn.data('grade');
            const badgeClass = btn.data('badge');
            $('#bdOverallBadge').text(grade).attr('class', `badge ms-1 ${badgeClass}`);

            const lessons = btn.data('lessons') || [];
            const tbody = $('#bdLessonsTableBody');
            tbody.empty();

            if (Array.isArray(lessons) && lessons.length > 0) {
                lessons.forEach((ls, i) => {
                    const lGrade = ls.grade || getLetterGrade(ls.score);
                    const lClass = getGradeBadgeClass(lGrade);
                    const stgBadge = ls.stage ? `<span class="badge bg-light text-dark border">${ls.stage}</span>` : '-';
                    const timeOutBadge = ls.time_out ? `<span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-clock me-1"></i>${ls.time_out}</span>` : '-';
                    tbody.append(`
                        <tr>
                            <td>${i + 1}</td>
                            <td>${stgBadge}</td>
                            <td class="fw-medium">${ls.lesson}</td>
                            <td class="text-center">${timeOutBadge}</td>
                            <td class="text-center font-monospace fw-bold">${ls.score} / 100</td>
                            <td class="text-center"><span class="badge ${lClass}">${lGrade}</span></td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="6" class="text-center text-muted">No lesson details available.</td></tr>');
            }

            const remarks = btn.data('remarks');
            if (remarks) {
                $('#bdRemarksText').text(remarks);
                $('#bdRemarksContainer').removeClass('d-none');
            } else {
                $('#bdRemarksContainer').addClass('d-none');
            }

            $('#viewBreakdownModal').modal('show');
        });
    </script>
</body>

</html>
