<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Instructor - Aircraft Logbook</title>
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="page-header">
            <h2>Aircraft Logbook Entry</h2>
            <p>Record flight duration, aircraft used, and block numbers.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Aircraft Logbook Entry</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Aircraft Logbook Entries</p>
                    <p class="panel-subtitle">Latest recorded flight logs submitted by you.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#addLogbookModal"><i
                        class="bi bi-plus-lg"></i> Add Logbook Entry</button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorLogbookTable">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Aircraft</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Block Off / On</th>
                            <th>Take Off / Landing</th>
                            <th>Block Time</th>
                            <th>Flight Time</th>
                            <th>Fuel (gal)</th>
                            <th>Technical Issues</th>
                            <th>Mechanics</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logbooks as $log)
                            <tr>
                                <td data-order="{{ $log->date_time }}">
                                    {{ \Carbon\Carbon::parse($log->date_time)->format('M d, Y h:i A') }}
                                </td>
                                <td class="fw-semibold">{{ $log->aircraft }}</td>
                                <td>{{ $log->student_name }}</td>
                                <td>{{ $log->instructor_name }}</td>
                                <td class="text-nowrap"><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->block_off_start }} -
                                        {{ $log->block_on_off }}</span></td>
                                <td class="text-nowrap"><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->take_off }} -
                                        {{ $log->landing }}</span></td>
                                <td><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->block_time }}</span>
                                </td>
                                <td><span
                                        class="badge bg-light text-dark border px-2 py-1">{{ $log->flight_time }}</span>
                                </td>
                                <td>{{ $log->fuel_used_gal }}</td>
                                <td>{{ $log->technical_issues ?? 'N/A' }}</td>

                                <td>{{ $log->mechanics ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Form -->
    <div class="modal fade" id="addLogbookModal" tabindex="-1" aria-labelledby="addLogbookModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLogbookModalLabel"><i class="bi bi-journal-plus me-2"></i>Add
                        Aircraft Logbook Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('instructor.aircraft.logbooks.store') }}" method="POST" id="addLogbookForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="aircraft" class="form-label fw-semibold">Aircraft Registration</label>
                                <input type="text" class="form-control" id="aircraft" name="aircraft"
                                    placeholder="Type aircraft manually (e.g. RP-C1721)" required>
                            </div>
                            <div class="col-md-6">
                                <label for="date_time" class="form-label fw-semibold">Date and Time</label>
                                <input type="datetime-local" class="form-control" id="date_time" name="date_time"
                                    required>
                            </div>

                            <div class="col-md-12">
                                <label for="student_id" class="form-label fw-semibold">Student</label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="" selected disabled>Select Student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->first_name }} {{ $student->middle_name }}
                                            {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="block_off_start" class="form-label fw-semibold">Block Off Start</label>
                                <input type="number" class="form-control" id="block_off_start"
                                    name="block_off_start" placeholder="e.g. 1000" required>
                            </div>
                            <div class="col-md-3">
                                <label for="block_on_off" class="form-label fw-semibold">Block On / Off</label>
                                <input type="number" class="form-control" id="block_on_off" name="block_on_off"
                                    placeholder="e.g. 1200 or 2200" required>
                            </div>
                            <div class="col-md-3">
                                <label for="take_off" class="form-label fw-semibold">Take Off Time</label>
                                <input type="number" class="form-control" id="take_off" name="take_off"
                                    placeholder="e.g. 1000" required>
                            </div>
                            <div class="col-md-3">
                                <label for="landing" class="form-label fw-semibold">Landing Time</label>
                                <input type="number" class="form-control" id="landing" name="landing"
                                    placeholder="e.g. 1010" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Calculated Block Time</label>
                                <input type="number" class="form-control bg-light fw-bold" id="block_time_display"
                                    value="0" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Calculated Flight Time</label>
                                <input type="number" class="form-control bg-light fw-bold" id="flight_time_display"
                                    value="0" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="fuel_used_gal" class="form-label fw-semibold">Fuel Used (gallons)</label>
                                <input type="number" class="form-control" id="fuel_used_gal" name="fuel_used_gal"
                                    min="0" step="0.1" placeholder="e.g. 15.5" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Aircraft Logbook</button>
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
        const instructorLogbookTable = document.getElementById('instructorLogbookTable');
        if (instructorLogbookTable && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(instructorLogbookTable).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false
            });
        }

        function calculateTimes() {
            const blockOff = parseFloat(document.getElementById('block_off_start').value);
            const blockOn = parseFloat(document.getElementById('block_on_off').value);
            const takeOff = parseFloat(document.getElementById('take_off').value);
            const landing = parseFloat(document.getElementById('landing').value);

            if (!isNaN(blockOff) && !isNaN(blockOn)) {
                document.getElementById('block_time_display').value = blockOff + blockOn;
            } else {
                document.getElementById('block_time_display').value = 0;
            }

            if (!isNaN(takeOff) && !isNaN(landing)) {
                document.getElementById('flight_time_display').value = Math.abs(takeOff - landing);
            } else {
                document.getElementById('flight_time_display').value = 0;
            }
        }

        ['block_off_start', 'block_on_off', 'take_off', 'landing'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', calculateTimes);
                el.addEventListener('keyup', calculateTimes);
            }
        });
    </script>
</body>

</html>
