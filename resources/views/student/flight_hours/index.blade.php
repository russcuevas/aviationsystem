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
            <h2>Flight Hours Tracking</h2>
            <p>Track your accumulated and remaining required flight hours.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Flight Hours Tracking</span></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-stopwatch"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($totalAccumulatedHours, 1) }} hrs</div>
                        <div class="stat-label">Total Accumulated Hours</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ number_format($remainingHours, 1) }} hrs</div>
                        <div class="stat-label">Remaining Required Hours (Req: {{ $requiredHours }} hrs)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">My Flight Hour Records</p>
                    <p class="panel-subtitle">Validated and pending session hours.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="studentHoursTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Instructor</th>
                            <th>Aircraft</th>
                            <th>Stage</th>
                            <th>Lesson</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($flightRecords as $record)
                            <tr>
                                <td data-order="{{ $record['date'] }}">
                                    {{ \Carbon\Carbon::parse($record['date'])->format('M j, Y') }}
                                </td>
                                <td>{{ $record['instructor'] }}</td>
                                <td>{{ $record['aircraft'] }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $record['stage'] }}</span>
                                </td>
                                <td class="fw-semibold text-dark">{{ $record['lesson'] }}</td>
                                <td class="fw-bold text-primary">{{ number_format($record['hours'], 1) }} hrs</td>
                                <td>
                                    @if (strtolower($record['status']) === 'completed' || strtolower($record['status']) === 'validated')
                                        <span class="school-status status-active"><i class="bi bi-check-circle-fill me-1"></i>Validated</span>
                                    @elseif (strtolower($record['status']) === 'pending review' || strtolower($record['status']) === 'for review' || strtolower($record['status']) === 'completed (pending approval)')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1" style="font-size:0.78rem;">
                                            <i class="bi bi-hourglass-split me-1"></i>Pending Validation
                                        </span>
                                    @else
                                        <span class="school-status status-onleave"><i class="bi bi-clock me-1"></i>{{ $record['status'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('script.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($('#studentHoursTable').length && $.fn.DataTable) {
                $('#studentHoursTable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'desc']
                    ],
                    autoWidth: false,
                    destroy: true,
                    language: {
                        emptyTable: "No flight hour records found."
                    }
                });
            }
        });
    </script>
</body>

</html>
