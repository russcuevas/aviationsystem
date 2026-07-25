<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Admin - Flight Hours Validation</title>
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
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
            <h2>Flight Hours Validation</h2>
            <p>Review and confirm logged flight hours entries.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Overview<i
                    class="bi bi-chevron-right"></i><span>Flight Hours Validation</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Validation Queue</p>
                    <p class="panel-subtitle">Approve or cancel submitted flight-hour logs.</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="validationTable">
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Aircraft</th>
                            <th>Dual Inst.</th>
                            <th>PIC Time</th>
                            <th>Solo Time</th>
                            <th>Inst. Flight</th>
                            <th>Total Time</th>
                            <th>Review Status</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($flightHours as $hour)
                            <tr>
                                <td><span class="fw-semibold text-primary">{{ $hour->log_id }}</span></td>
                                <td data-order="{{ $hour->created_at ? $hour->created_at->format('Y-m-d') : '' }}">
                                    {{ $hour->created_at ? $hour->created_at->format('M d, Y') : '-' }}
                                </td>
                                <td>{{ $hour->student ? $hour->student->first_name . ' ' . $hour->student->last_name : 'N/A' }}</td>
                                <td>{{ $hour->aircraft ? $hour->aircraft->registration : 'N/A' }}</td>
                                <td>{{ $hour->dual_instruction_time !== null ? number_format($hour->dual_instruction_time, 1) . ' hrs' : '-' }}</td>
                                <td>{{ $hour->pic_time !== null ? number_format($hour->pic_time, 1) . ' hrs' : '-' }}</td>
                                <td>{{ $hour->solo_time !== null ? number_format($hour->solo_time, 1) . ' hrs' : '-' }}</td>
                                <td>{{ $hour->instrument_flight_time !== null ? number_format($hour->instrument_flight_time, 1) . ' hrs' : '-' }}</td>
                                <td><strong>{{ number_format($hour->total_time, 1) }} hrs</strong></td>
                                <td>
                                    @if ($hour->status === 'pending review')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-clock-history me-1"></i>pending review</span>
                                    @elseif ($hour->status === 'approved')
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>approved</span>
                                    @elseif ($hour->status === 'cancelled')
                                        <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>cancelled</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">{{ $hour->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $hour->remarks ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.flight.hours.update.status', $hour->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if ($hour->status !== 'approved')
                                            <button type="submit" name="status" value="approved" class="btn btn-sm btn-success py-1 px-2 me-1" title="Approve">
                                                <i class="bi bi-check-circle me-1"></i>Approve
                                            </button>
                                        @endif
                                        @if ($hour->status !== 'cancelled')
                                            <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-danger py-1 px-2" title="Cancel">
                                                <i class="bi bi-x-circle me-1"></i>Cancel
                                            </button>
                                        @endif
                                    </form>
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
        const validationTableEl = document.getElementById('validationTable');
        if (validationTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(validationTableEl).DataTable({
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
                autoWidth: false
            });
        }
    </script>
</body>

</html>
