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
            <h2>Flight Hours Encoding</h2>
            <p>Input actual flight hours for each student session.</p>
            <div class="page-breadcrumb"><i class="bi bi-grid-1x2-fill"></i>Dashboard<i
                    class="bi bi-chevron-right"></i><span>Flight Hours Encoding</span></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Encoded Flight Hours</p>
                    <p class="panel-subtitle">Recent sessions encoded for validation and records.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#logHoursModal"><i
                        class="bi bi-plus-lg"></i> Log Hours</button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table" id="instructorHoursTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Hours</th>
                            <th>Student</th>
                            <th>Aircraft</th>
                            <th>Flight Type</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-order="2026-04-03">Apr 3, 2026</td>
                            <td>1.4 hrs</td>
                            <td>Juan Dela Cruz</td>
                            <td>RP-C1721</td>
                            <td>Pattern Training</td>
                            <td>Takeoff and landing pattern drills.</td>
                        </tr>
                        <tr>
                            <td data-order="2026-04-02">Apr 2, 2026</td>
                            <td>1.8 hrs</td>
                            <td>Maria Reyes</td>
                            <td>RP-PA281</td>
                            <td>Cross Country</td>
                            <td>Navigation and diversion checkpoint validation.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="logHoursModal" tabindex="-1" aria-labelledby="logHoursModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logHoursModalLabel">Log Flight Hours</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="logHoursForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="hoursDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="hoursDate" name="hoursDate" required>
                            </div>
                            <div class="col-md-4">
                                <label for="hoursValue" class="form-label">Hours</label>
                                <input type="number" class="form-control" id="hoursValue" name="hoursValue"
                                    min="0.1" step="0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="hoursStudent" class="form-label">Student</label>
                                <select class="form-select" id="hoursStudent" name="hoursStudent" required>
                                    <option value="" selected disabled>Select student</option>
                                    <option value="Juan Dela Cruz">Juan Dela Cruz</option>
                                    <option value="Maria Reyes">Maria Reyes</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="hoursAircraft" class="form-label">Aircraft</label>
                                <select class="form-select" id="hoursAircraft" name="hoursAircraft" required>
                                    <option value="" selected disabled>Select aircraft</option>
                                    <option value="RP-C1721">RP-C1721</option>
                                    <option value="RP-PA281">RP-PA281</option>
                                    <option value="RP-C1508">RP-C1508</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="hoursFlightType" class="form-label">Flight Type</label>
                                <input type="text" class="form-control" id="hoursFlightType" name="hoursFlightType"
                                    required>
                            </div>

                            <div class="col-12">
                                <label for="hoursRemarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="hoursRemarks" name="hoursRemarks" rows="3"
                                    placeholder="Add notes about the flight session."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Hours</button>
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
        const instructorHoursTableEl = document.getElementById('instructorHoursTable');
        if (instructorHoursTableEl && window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(instructorHoursTableEl).DataTable({
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
