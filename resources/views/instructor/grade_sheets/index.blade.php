<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor - Grade Sheet Submission</title>
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
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
            <h2>Grade Sheet Submission</h2>
            <p>Evaluate student performance and submit grade sheets for approval.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Grade Sheet Submission</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Submitted Grade Sheets</p>
                    <p class="panel-subtitle">Recently submitted evaluations and review status.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#submitGradeModal">
                    <i class="bi bi-plus-lg me-1"></i> Submit Grade Sheet
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorGradeTable">
                    <thead>
                        <tr>
                            <th>Sheet ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Stage</th>
                            <th>Evaluated Lessons</th>
                            <th>Lesson Time Outs</th>
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
                                <td>
                                    @php
                                        $timeouts = [];
                                        if (is_array($sheet->lesson_grades)) {
                                            foreach ($sheet->lesson_grades as $lg) {
                                                if (!empty($lg['time_out'])) {
                                                    $timeouts[] = $lg['time_out'];
                                                }
                                            }
                                        }
                                        $timeoutDisplay = count($timeouts) > 0 ? implode(', ', $timeouts) : '-';
                                    @endphp
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-clock me-1"></i>{{ $timeoutDisplay }}</span>
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
                                    @if ($sheet->status === 'Accepted' || $sheet->status === 'Approved')
                                        <span class="school-status status-active">Accepted</span>
                                    @elseif ($sheet->status === 'For Review' || $sheet->status === 'Pending')
                                        <span class="school-status status-onleave">For Review</span>
                                    @else
                                        <span class="school-status status-inactive">{{ $sheet->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $stgDisp = $sheet->stage ? $sheet->stage->stage : (count($uniqueStages ?? []) > 0 ? implode(', ', $uniqueStages) : 'All Stages (Combined)');
                                    @endphp
                                    <button class="btn-review btn-view-breakdown" type="button"
                                        data-sheetid="{{ $sheet->sheet_id }}"
                                        data-student="{{ $sheet->student ? $sheet->student->first_name . ' ' . $sheet->student->last_name : 'N/A' }}"
                                        data-stage="{{ $stgDisp }}"
                                        data-avg="{{ number_format($sheet->total_score, 1) }}"
                                        data-grade="{{ $sheet->overall_grade }}"
                                        data-badge="{{ $badgeClass }}"
                                        data-lessons="{{ json_encode($sheet->lesson_grades) }}"
                                        data-remarks="{{ $sheet->remarks }}">
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

    <!-- Submit Grade Sheet Modal -->
    <div class="modal fade" id="submitGradeModal" tabindex="-1" aria-labelledby="submitGradeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="submitGradeModalLabel"><i class="bi bi-journal-check text-primary me-2"></i>Submit Grade Sheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('instructor.grade.sheet.store') }}" method="POST">
                    @csrf
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="date" class="form-label fw-medium">Evaluation Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" required value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-4">
                                <label for="student_id" class="form-label fw-medium">Select Student <span class="text-danger">*</span></label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="" selected disabled>Choose student...</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" data-stages="{{ json_encode($student->stages) }}" data-submitted-lessons="{{ json_encode($student->submitted_lessons ?? []) }}">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="stage_id" class="form-label fw-medium">Training Stage <span class="text-danger">*</span></label>
                                <select class="form-select" id="stage_id" name="stage_id" required disabled>
                                    <option value="" selected disabled>Select student first</option>
                                </select>
                            </div>
                        </div>

                        <!-- Dynamic Lessons List & Grade Inputs -->
                        <div id="lessons_section" class="d-none">
                            <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">
                                <i class="bi bi-list-check me-1"></i> Lesson Evaluation & Individual Time Outs
                            </h6>
                            <div id="lessons_list_container" class="vstack gap-2 mb-4"></div>

                            <!-- Live Computation Summary Card -->
                            <div class="card border-0 bg-light p-3 mb-3 shadow-sm rounded-3">
                                <div class="row align-items-center text-center">
                                    <div class="col-6 border-end">
                                        <div class="text-muted small uppercase fw-semibold">Combined Average Score</div>
                                        <div class="fs-4 fw-extrabold text-primary" id="computed_avg_score">0.0 / 100</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small uppercase fw-semibold">Computed Overall Grade</div>
                                        <div>
                                            <span class="badge bg-secondary fs-5 px-3 py-1" id="computed_overall_grade">N/A</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-medium">Instructor Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2"
                                placeholder="Add optional comments or performance evaluation details."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitGrade" disabled><i class="bi bi-check-lg me-1"></i> Submit Evaluation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Breakdown Modal -->
    <div class="modal fade" id="viewBreakdownModal" tabindex="-1" aria-labelledby="viewBreakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="viewBreakdownModalLabel"><i class="bi bi-file-text text-primary me-2"></i>Grade Sheet Breakdown</h5>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        const instructorGradeTableEl = document.getElementById('instructorGradeTable');
        if (instructorGradeTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(instructorGradeTableEl).DataTable({
                pageLength: 10,
                order: [
                    [1, 'desc']
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

        let currentStudentStages = [];
        let currentSubmittedLessons = {};

        // 1. Student dropdown change -> load Stages
        $('#student_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            try {
                currentStudentStages = selectedOption.data('stages') || [];
                currentSubmittedLessons = selectedOption.data('submitted-lessons') || {};
            } catch (e) {
                currentStudentStages = [];
                currentSubmittedLessons = {};
            }

            const stageSelect = $('#stage_id');
            stageSelect.empty();
            $('#lessons_section').addClass('d-none');
            $('#lessons_list_container').empty();
            $('#btnSubmitGrade').prop('disabled', true);

            if (currentStudentStages && currentStudentStages.length > 0) {
                stageSelect.prop('disabled', false);
                stageSelect.append('<option value="" selected disabled>Select stage...</option>');
                stageSelect.append('<option value="all" class="fw-bold text-primary">★ All Stages (Combined Evaluation)</option>');
                currentStudentStages.forEach(stg => {
                    stageSelect.append(`<option value="${stg.id}">${stg.stage}</option>`);
                });
            } else {
                stageSelect.prop('disabled', true);
                stageSelect.append('<option value="" selected disabled>No stages found for this student</option>');
            }
        });

        // 2. Stage dropdown change -> render Lessons list & Grade inputs with per-lesson Time Out
        $('#stage_id').on('change', function() {
            const selectedStageId = $(this).val();
            const container = $('#lessons_list_container');
            container.empty();

            let lessonsToRender = [];

            if (selectedStageId === 'all') {
                currentStudentStages.forEach(stg => {
                    let lessons = stg.lessons || [];
                    lessons.forEach(lsn => {
                        lessonsToRender.push({
                            stage_name: stg.stage,
                            lesson_name: lsn
                        });
                    });
                });
            } else {
                const selectedStageObj = currentStudentStages.find(s => s.id == selectedStageId);
                let lessons = selectedStageObj && selectedStageObj.lessons ? selectedStageObj.lessons : [];
                lessons.forEach(lsn => {
                    lessonsToRender.push({
                        stage_name: selectedStageObj ? selectedStageObj.stage : '',
                        lesson_name: lsn
                    });
                });
            }

            if (lessonsToRender && lessonsToRender.length > 0) {
                $('#lessons_section').removeClass('d-none');

                let unsubmittedCount = 0;

                lessonsToRender.forEach((item, idx) => {
                    const lsn = item.lesson_name;
                    const stgName = item.stage_name;
                    const cleanLsn = (lsn || '').trim();
                    const submittedInfo = currentSubmittedLessons[cleanLsn] || currentSubmittedLessons[lsn];

                    if (submittedInfo) {
                        const prevScore = submittedInfo.score !== null ? submittedInfo.score : '-';
                        const prevGrade = submittedInfo.grade || getLetterGrade(prevScore);
                        const badgeClass = getGradeBadgeClass(prevGrade);
                        const statusText = submittedInfo.status === 'Accepted' ? 'Accepted' : (submittedInfo.status || 'Submitted');
                        const statusBadgeClass = submittedInfo.status === 'Accepted' ? 'bg-success' : 'bg-info text-dark';

                        const rowHtml = `
                            <div class="row align-items-center g-2 p-2 border rounded bg-light shadow-sm opacity-75">
                                <div class="col-md-4 col-12">
                                    <span class="badge bg-light text-dark border me-1">${stgName}</span>
                                    <label class="fw-semibold mb-0 text-dark small"><i class="bi bi-journal-text me-1 text-primary"></i> ${lsn}</label>
                                    <span class="badge ${statusBadgeClass} ms-1" style="font-size:0.75rem;"><i class="bi bi-check-circle-fill me-1"></i>${statusText}</span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text" title="Time Out"><i class="bi bi-clock"></i></span>
                                        <input type="time" class="form-control" disabled title="Already submitted">
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control text-center fw-bold" value="${prevScore}" disabled placeholder="0 - 100">
                                        <span class="input-group-text">/ 100</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-12 text-end">
                                    <span class="badge ${badgeClass} px-3 py-2" style="font-size:0.85rem;">${prevGrade}</span>
                                </div>
                            </div>
                        `;
                        container.append(rowHtml);
                    } else {
                        unsubmittedCount++;
                        const rowHtml = `
                            <div class="row align-items-center g-2 p-2 border rounded bg-white shadow-sm">
                                <div class="col-md-4 col-12">
                                    <span class="badge bg-light text-dark border me-1">${stgName}</span>
                                    <label class="fw-semibold mb-0 text-dark small"><i class="bi bi-journal-text me-1 text-primary"></i> ${lsn}</label>
                                    <input type="hidden" name="lesson_stages[${lsn}]" value="${stgName}">
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text" title="Time Out"><i class="bi bi-clock"></i></span>
                                        <input type="time" class="form-control" name="lesson_timeouts[${lsn}]" title="Time Out for this lesson">
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control lesson-score-input" 
                                               name="scores[${lsn}]" min="0" max="100" step="0.5" placeholder="0 - 100">
                                        <span class="input-group-text">/ 100</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-12 text-end">
                                    <span class="badge bg-secondary px-3 py-2 lesson-letter-badge" style="font-size:0.85rem;">N/A</span>
                                </div>
                            </div>
                        `;
                        container.append(rowHtml);
                    }
                });

                if (unsubmittedCount === 0) {
                    container.prepend(`
                        <div class="alert alert-info py-2 px-3 small mb-2 border-0 bg-info-subtle text-info-emphasis rounded-3">
                            <i class="bi bi-check-all me-1"></i> All lessons in this stage have already been submitted for this student.
                        </div>
                    `);
                    $('#btnSubmitGrade').prop('disabled', true);
                } else {
                    $('#btnSubmitGrade').prop('disabled', false);
                }

                recalculateTotalGrade();
            } else {
                $('#lessons_section').addClass('d-none');
                $('#btnSubmitGrade').prop('disabled', true);
            }
        });

        // 3. Live computation on inputting score
        $(document).on('input change', '.lesson-score-input', function() {
            const scoreVal = $(this).val();
            const letterGrade = getLetterGrade(scoreVal);
            const badgeClass = getGradeBadgeClass(letterGrade);

            const badgeEl = $(this).closest('.row').find('.lesson-letter-badge');
            badgeEl.text(letterGrade).attr('class', `badge px-3 py-2 lesson-letter-badge ${badgeClass}`);

            recalculateTotalGrade();
        });

        // Recalculate Average & Overall Grade
        function recalculateTotalGrade() {
            let total = 0;
            let count = 0;

            $('.lesson-score-input').each(function() {
                const val = $(this).val();
                if (val !== '' && !isNaN(val)) {
                    total += parseFloat(val);
                    count++;
                }
            });

            if (count > 0) {
                const avg = (total / count).toFixed(1);
                const overallGrade = getLetterGrade(avg);
                const badgeClass = getGradeBadgeClass(overallGrade);

                $('#computed_avg_score').text(`${avg} / 100`);
                $('#computed_overall_grade').text(overallGrade).attr('class', `badge fs-5 px-3 py-1 ${badgeClass}`);
            } else {
                $('#computed_avg_score').text('0.0 / 100');
                $('#computed_overall_grade').text('N/A').attr('class', 'badge bg-secondary fs-5 px-3 py-1');
            }
        }

        // 4. View Breakdown Modal
        $(document).on('click', '.btn-view-breakdown', function() {
            const btn = $(this);
            $('#bdSheetId').text(btn.data('sheetid'));
            $('#bdStudentName').text(btn.data('student'));
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
