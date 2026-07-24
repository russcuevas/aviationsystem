<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP</title>
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
            <h2>Flight Hours</h2>
            <p>Superadmin view-only monitoring of logged flight hours.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-grid-1x2-fill"></i>
                Overview
                <i class="bi bi-chevron-right"></i>
                <span>Flight Hours</span>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Flight Hours Log</p>
                    <p class="panel-subtitle">View-only table for superadmin users.</p>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table flight-hours-table" id="flightHoursTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Aircraft</th>
                            <th>Dual Instruction Time</th>
                            <th>PIC Time</th>
                            <th>Solo Time</th>
                            <th>Instrument Flight Time</th>
                            <th>Total Time</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-order="2026-04-02"><span class="fh-date">02/04/2026</span></td>
                            <td>
                                <div class="fh-student">
                                    <span class="fh-student-name">Juan Dela Cruz</span>
                                </div>
                            </td>
                            <td><span class="fh-aircraft">RP-C1721</span></td>
                            <td><span class="fh-hours">1.5 hrs</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">1.5 hrs</span></td>
                            <td><span class="fh-remarks">Pattern and landing drills</span></td>
                        </tr>
                        <tr>
                            <td data-order="2026-04-01"><span class="fh-date">01/04/2026</span></td>
                            <td>
                                <div class="fh-student">
                                    <span class="fh-student-name">Maria Reyes</span>
                                </div>
                            </td>
                            <td><span class="fh-aircraft">RP-PA281</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">2.0 hrs</span></td>
                            <td><span class="fh-hours">2.0 hrs</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">2.0 hrs</span></td>
                            <td><span class="fh-remarks">Navigation and waypoint tracking</span></td>
                        </tr>
                        <tr>
                            <td data-order="2026-03-31"><span class="fh-date">31/03/2026</span></td>
                            <td>
                                <div class="fh-student">
                                    <span class="fh-student-name">Carlos Santos</span>
                                </div>
                            </td>
                            <td><span class="fh-aircraft">RP-DA401</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">1.2 hrs</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">1.2 hrs</span></td>
                            <td><span class="fh-hours">1.2 hrs</span></td>
                            <td><span class="fh-remarks">Partial panel procedure practice</span></td>
                        </tr>
                        <tr>
                            <td data-order="2026-03-30"><span class="fh-date">30/03/2026</span></td>
                            <td>
                                <div class="fh-student">
                                    <span class="fh-student-name">Ana Lim</span>
                                </div>
                            </td>
                            <td><span class="fh-aircraft">RP-C1508</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">1.7 hrs</span></td>
                            <td><span class="fh-hours">1.7 hrs</span></td>
                            <td><span class="fh-hours">0.0 hrs</span></td>
                            <td><span class="fh-hours">1.7 hrs</span></td>
                            <td><span class="fh-remarks">Traffic pattern and emergency drills</span></td>
                        </tr>
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
        const flightHoursTableElement = document.getElementById('flightHoursTable');
        let flightHoursDataTable;

        function initFlightHoursTable() {
            if (!flightHoursTableElement || !window.jQuery || !window.jQuery.fn.DataTable) {
                return;
            }

            if (flightHoursDataTable) {
                flightHoursDataTable.destroy();
            }

            flightHoursDataTable = window.jQuery(flightHoursTableElement).DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false,
            });
        }

        initFlightHoursTable();
    </script>
</body>

</html>
