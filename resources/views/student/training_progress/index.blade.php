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
            <h2>Training Progress</h2>
            <p>Review your completed and pending lessons across training modules.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Training Progress</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Lesson Progress Tracker</p>
                    <p class="panel-subtitle">Current completion status for each lesson.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="studentProgressTable">
                    <thead>
                        <tr>
                            <th>Stage</th>
                            <th>Lesson</th>
                            <th>Instructor</th>
                            <th>Date Updated</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($progressList as $item)
                            <tr>
                                <td><span class="badge bg-light text-dark border px-2 py-1">{{ $item['stage'] }}</span>
                                </td>
                                <td class="fw-semibold">{{ $item['lesson'] }}</td>
                                <td>{{ $item['instructor'] }}</td>
                                <td data-order="{{ $item['date_raw'] }}">{{ $item['date_updated'] }}</td>
                                <td>
                                    @if ($item['status'] === 'Completed')
                                        <span class="school-status status-active"><i
                                                class="bi bi-check-circle-fill me-1"></i>Completed</span>
                                    @elseif ($item['status'] === 'In Progress')
                                        <span class="school-status status-inactive"><i class="bi bi-clock me-1"></i>In
                                            Progress</span>
                                    @else
                                        <span class="school-status status-inactive">{{ $item['status'] }}</span>
                                    @endif
                                </td>
                                <td class="small">
                                    {{ !empty($item['remarks']) && strtolower(trim($item['remarks'])) !== 'none' ? $item['remarks'] : 'N/A' }}
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
            if ($('#studentProgressTable').length && $.fn.DataTable) {
                $('#studentProgressTable').DataTable({
                    pageLength: 10,
                    order: [
                        [3, 'desc']
                    ],
                    autoWidth: false,
                    destroy: true,
                    language: {
                        emptyTable: "No lesson progress records found."
                    }
                });
            }
        });
    </script>
</body>

</html>
