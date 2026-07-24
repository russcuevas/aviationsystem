<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor</title>
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
            <h2>Schedule Viewing</h2>
            <p>View assigned flights and request schedule adjustments when necessary.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Schedule Viewing</span></div>
        </div>

        <div class="panel mb-3">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Assigned Flight Schedules</p>
                    <p class="panel-subtitle">Daily and weekly schedule of your assigned students and aircraft.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorScheduleTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Student</th>
                            <th>Stage</th>
                            <th>Aircraft</th>
                            <th>Lesson</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $sched)
                        <tr>
                            <td data-order="{{ $sched->date }}">{{ \Carbon\Carbon::parse($sched->date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sched->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($sched->end_time)->format('h:i A') }}</td>
                            <td class="fw-semibold">{{ $sched->student_name }}</td>
                            <td><span class="badge bg-light text-dark border px-2 py-1">{{ $sched->stage_name }}</span></td>
                            <td>{{ $sched->aircraft_reg }}</td>
                            <td>{{ $sched->lesson_type }}</td>
                            <td>
                                @if($sched->status === 'Completed' || $sched->status === 'Confirmed')
                                    <span class="school-status status-active">{{ $sched->status }}</span>
                                @elseif($sched->status === 'Scheduled' || $sched->status === 'In Progress')
                                    <span class="school-status status-onleave">{{ $sched->status }}</span>
                                @else
                                    <span class="school-status status-inactive">{{ $sched->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No flight schedules assigned to you yet.</td>
                        </tr>
                        @endforelse
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
        const scheduleTableEl = document.getElementById('instructorScheduleTable');
        if (scheduleTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(scheduleTableEl).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
