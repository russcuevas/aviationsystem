<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Superadmin - Grade Sheet Validation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
    <!-- ================= SIDEBAR ================= -->
    @include('superadmin.components.left_sidebar')

    <!-- ================= TOPBAR ================= -->
    @include('superadmin.components.topbar')

    <main class="main-content">
        <div class="page-header">
            <h2>Grade Sheet Validation</h2>
            <p>Audit and review grade sheets submitted across all training providers.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Grade Sheets</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Grade Sheet Audit Queue</p>
                    <p class="panel-subtitle">Compressed student evaluation summary across training providers.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="gradeSheetsTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Training Provider</th>
                            <th>Grade Sheets Summary</th>
                            <th>Overall Avg Score</th>
                            <th>Overall Grade</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compressedStudents as $st)
                            <tr>
                                <td><span class="fw-semibold text-primary">{{ $st->student_code }}</span></td>
                                <td class="fw-semibold">{{ $st->student_name }}</td>
                                <td>
                                    <span class="badge bg-primary px-2 py-1" style="background-color: var(--cobalt) !important;">
                                        {{ $st->provider_name }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge bg-secondary px-2 py-1"><i class="bi bi-folder2-open me-1"></i>{{ $st->total_sheets }} {{ Str::plural('Sheet', $st->total_sheets) }}</span>
                                        @if($st->accepted_count > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.75rem;"><i class="bi bi-check-circle-fill me-1"></i>{{ $st->accepted_count }} Accepted</span>
                                        @endif
                                        @if($st->for_review_count > 0)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1" style="font-size:0.75rem;"><i class="bi bi-hourglass-split me-1"></i>{{ $st->for_review_count }} Review</span>
                                        @endif
                                        @if($st->rejected_count > 0)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size:0.75rem;"><i class="bi bi-x-circle-fill me-1"></i>{{ $st->rejected_count }} Rejected</span>
                                        @endif
                                    </div>
                                </td>
                                <td><strong>{{ number_format($st->overall_avg, 1) }} / 100</strong></td>
                                <td>
                                    @php
                                        $g = $st->overall_grade;
                                        $badgeClass = 'bg-secondary';
                                        if (in_array($g, ['A+', 'A'])) $badgeClass = 'bg-success';
                                        elseif (in_array($g, ['B+', 'B'])) $badgeClass = 'bg-primary';
                                        elseif (in_array($g, ['C+', 'C'])) $badgeClass = 'bg-warning text-dark';
                                        elseif ($g === 'F') $badgeClass = 'bg-danger';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1">{{ $g }}</span>
                                </td>
                                <td>
                                    <button class="btn-review btn-view-student-sheets" type="button"
                                        data-student="{{ $st->student_name }}"
                                        data-provider="{{ $st->provider_name }}"
                                        data-sheets="{{ json_encode($st->sheets_list) }}">
                                        <i class="bi bi-layers-fill me-1"></i> View Grade Sheets ({{ $st->total_sheets }})
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Student Grade Sheets List Modal -->
    <div class="modal fade" id="studentGradeSheetsModal" tabindex="-1" aria-labelledby="studentGradeSheetsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="studentGradeSheetsModalLabel">
                        <i class="bi bi-folder-symlink text-primary me-2"></i>Student Evaluation History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border flex-wrap gap-2">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Student Name</div>
                            <div class="fw-bold text-dark fs-5" id="sgsModalStudentName">-</div>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Training Provider</div>
                            <div><span class="badge bg-primary px-3 py-2" id="sgsModalProviderName" style="background-color: var(--cobalt) !important;">-</span></div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>Sheet ID</th>
                                    <th>Date</th>
                                    <th>Instructor</th>
                                    <th>Stage</th>
                                    <th>Evaluated Lessons</th>
                                    <th>Score</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="sgsTableBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Breakdown Modal -->
    <div class="modal fade" id="viewBreakdownModal" tabindex="-1" aria-labelledby="viewBreakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="viewBreakdownModalLabel"><i class="bi bi-file-text text-primary me-2"></i>Grade Sheet Audit Breakdown</h5>
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
                            <div class="text-muted small">Training Provider</div>
                            <div><span class="badge bg-primary px-2 py-1" id="bdProviderName" style="background-color: var(--cobalt) !important;">-</span></div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-white">
                                <span class="text-muted small d-block">Instructor</span>
                                <span class="fw-medium text-dark" id="bdInstructorName">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-white">
                                <span class="text-muted small d-block">Stage</span>
                                <span class="fw-medium text-dark" id="bdStageName">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 mb-3 bg-primary-subtle text-primary border border-primary-subtle rounded d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold">Evaluation Score:</span>
                            <span class="fs-5 ms-1 fw-bold" id="bdAvgScore">-</span> / 100
                        </div>
                        <div>
                            <span class="fw-semibold">Overall Rating:</span>
                            <span class="badge bg-success ms-1" id="bdOverallBadge">-</span>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2">Evaluated Lessons</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Stage</th>
                                    <th>Lesson</th>
                                    <th class="text-center">Time Out</th>
                                    <th class="text-center" style="width: 100px;">Score</th>
                                    <th class="text-center" style="width: 80px;">Grade</th>
                                </tr>
                            </thead>
                            <tbody id="bdLessonsTableBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>

                    <div id="bdRemarksContainer" class="d-none">
                        <h6 class="fw-bold mb-1">Instructor Remarks</h6>
                        <div class="p-3 bg-light border rounded text-dark small" id="bdRemarksText">
                            -
                        </div>
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
        const gradeSheetsTableEl = document.getElementById('gradeSheetsTable');
        if (gradeSheetsTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(gradeSheetsTableEl).DataTable({
                pageLength: 10,
                order: [
                    [1, 'asc']
                ],
                autoWidth: false
            });
        }

        function getLetterGrade(score) {
            const s = parseFloat(score);
            if (isNaN(s)) return 'N/A';
            if (s >= 95) return 'A+';
            if (s >= 90) return 'A';
            if (s >= 85) return 'B+';
            if (s >= 80) return 'B';
            if (s >= 75) return 'C+';
            if (s >= 70) return 'C';
            return 'F';
        }

        function getGradeBadgeClass(grade) {
            if (['A+', 'A'].includes(grade)) return 'bg-success';
            if (['B+', 'B'].includes(grade)) return 'bg-primary';
            if (['C+', 'C'].includes(grade)) return 'bg-warning text-dark';
            if (grade === 'F') return 'bg-danger';
            return 'bg-secondary';
        }

        $(document).on('click', '.btn-view-student-sheets', function() {
            const btn = $(this);
            const studentName = btn.data('student');
            const providerName = btn.data('provider');
            const sheets = btn.data('sheets') || [];

            $('#sgsModalStudentName').text(studentName);
            $('#sgsModalProviderName').text(providerName);

            const tbody = $('#sgsTableBody');
            tbody.empty();

            if (Array.isArray(sheets) && sheets.length > 0) {
                sheets.forEach((s) => {
                    let statusBadge = `<span class="school-status status-inactive">${s.status}</span>`;
                    if (['Accepted', 'Approved'].includes(s.status)) {
                        statusBadge = `<span class="school-status status-active"><i class="bi bi-check-circle me-1"></i>Accepted</span>`;
                    } else if (['For Review', 'Pending'].includes(s.status)) {
                        statusBadge = `<span class="school-status status-onleave"><i class="bi bi-clock-history me-1"></i>For Review</span>`;
                    }

                    const badgeClass = getGradeBadgeClass(s.overall_grade);

                    tbody.append(`
                        <tr>
                            <td><span class="fw-semibold text-primary">${s.sheet_id}</span></td>
                            <td>${s.date}</td>
                            <td>${s.instructor_name}</td>
                            <td><span class="badge bg-light text-dark border px-2 py-1">${s.stage}</span></td>
                            <td><span class="badge bg-light text-secondary border">${s.lessons_count} Lessons</span></td>
                            <td><strong>${s.total_score} / 100</strong></td>
                            <td><span class="badge ${badgeClass}">${s.overall_grade}</span></td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn-review btn-view-breakdown" type="button"
                                    data-sheetid="${s.sheet_id}"
                                    data-student="${studentName}"
                                    data-provider="${providerName}"
                                    data-instructor="${s.instructor_name}"
                                    data-stage="${s.stage}"
                                    data-avg="${s.total_score}"
                                    data-grade="${s.overall_grade}"
                                    data-badge="${badgeClass}"
                                    data-lessons='${JSON.stringify(s.lesson_grades || [])}'
                                    data-remarks="${s.remarks || ''}">
                                    <i class="bi bi-eye me-1"></i> Breakdown
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="9" class="text-center text-muted">No grade sheets found for this student.</td></tr>');
            }

            $('#studentGradeSheetsModal').modal('show');
        });

        $(document).on('click', '.btn-view-breakdown', function() {
            const btn = $(this);
            $('#bdSheetId').text(btn.data('sheetid'));
            $('#bdStudentName').text(btn.data('student'));
            $('#bdProviderName').text(btn.data('provider'));
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
