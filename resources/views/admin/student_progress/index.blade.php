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
</head>

<body>

    @include('admin.components.left_sidebar')

    @include('admin.components.topbar')

    <main class="main-content">
        @if (isset($providerName))
            <span class="badge bg-primary px-3 py-2 mb-3"
                style="font-size: 0.9rem; font-weight: 600; border-radius: 8px; background-color: var(--cobalt) !important;">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ $providerName }}
            </span>
        @endif

        <div class="page-header">
            <h2>Student Progress Monitoring</h2>
            <p>Track training stages and identify students behind schedule.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Student Progress</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Progress Tracker</p>
                    <p class="panel-subtitle">Filter and monitor progress completion per student based on completed
                        lesson hours.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="progressTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Training Provider</th>
                            <th>Training Stage</th>
                            <th>Last Lesson</th>
                            <th>Total Hours</th>
                            <th>Progress</th>
                            <th>Schedule Health</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progressList as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->student_name }}</td>
                                <td>{{ $item->provider_name }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->stage_name }}</div>
                                    @if(strtolower($item->stage_status) === 'completed' || $item->progress_pct == 100)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 mt-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i>Completed
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 mt-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-hourglass-split me-1"></i>In Progress
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $item->last_lesson }}</td>
                                <td>{{ $item->total_hours_formatted }}</td>
                                <td data-order="{{ $item->progress_pct }}">
                                    <div class="progress-wrap">
                                        <div class="progress-bar-track">
                                            <div class="progress-bar-fill {{ $item->progress_pct == 100 ? 'bg-success' : '' }}" style="width:{{ $item->progress_pct }}%">
                                            </div>
                                        </div>
                                        <span class="progress-pct fw-bold">{{ $item->progress_pct }}%</span>
                                    </div>
                                    @if(isset($item->total_lessons) && $item->total_lessons > 0)
                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-journal-check me-1"></i>{{ $item->completed_lessons }}/{{ $item->total_lessons }} Lessons
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->health === 'On Track')
                                        <span class="school-status status-active">On Track</span>
                                    @elseif($item->health === 'Behind')
                                        <span class="school-status status-onleave">Behind</span>
                                    @else
                                        <span class="school-status status-inactive">{{ $item->health }}</span>
                                    @endif
                                </td>
                                </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        const progressTableEl = document.getElementById('progressTable');
        if (progressTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(progressTableEl).DataTable({
                pageLength: 10,
                order: [
                    [5, 'desc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
