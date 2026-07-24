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

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• MAIN CONTENT â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <main class="main-content">

        <!-- Page Header -->
        <div class="page-header">
            <h2>Training Provider</h2>
            <p>Regulatory oversight for accredited schools.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-grid-1x2-fill"></i>
                Overview
                <i class="bi bi-chevron-right"></i>
                <span>Training Provider</span>
            </div>
        </div>

        <!-- â”€â”€ Recent Training Progress Table â”€â”€ -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Provider Registry</p>
                    <p class="panel-subtitle">Search and sort accredited records.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
                    <i class="bi bi-plus-lg"></i>
                    Add Provider
                </button>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table" id="trainingTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Provider name</th>
                            <th class="progress-cell">Address</th>
                            <th>Email</th>
                            <th>Accreditation Course</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="school-code-wrap">
                                    <span class="school-code">STU-2024-001</span>
                                </div>
                            </td>
                            <td>
                                <div class="school-name-wrap">
                                    <span class="school-name">PhilSCA Villamor</span>
                                </div>
                            </td>
                            <td>
                                <div class="school-address">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>Villamor Airbase, Pasay City</span>
                                </div>
                            </td>
                            <td>
                                <a class="school-email"
                                    href="mailto:russelcuevas0@gmail.com">russelcuevas0@gmail.com</a>
                            </td>
                            <td>
                                MI
                            </td>
                            <td><span class="school-status status-active">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">View</i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <div class="modal fade" id="addSchoolModal" tabindex="-1" aria-labelledby="addSchoolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSchoolModalLabel">Add School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSchoolForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="schoolName" class="form-label">School Name</label>
                                <input type="text" class="form-control" id="schoolName" name="schoolName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="schoolCode" class="form-label">School Code</label>
                                <input type="text" class="form-control" id="schoolCode" name="schoolCode" required>
                            </div>


                            <div class="col-md-6">
                                <label for="schoolAddress" class="form-label">Address</label>
                                <input type="text" class="form-control" id="schoolAddress" name="schoolAddress"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="contactEmail" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contactEmail" name="contactEmail"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="contactPhone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="contactPhone" name="contactPhone"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="accreditation_course" class="form-label">Accreditation Course</label>
                                <select class="form-select" id="accreditation_course" name="accreditation_course"
                                    required>
                                    <option value="" selected disabled>Select accreditation course</option>
                                    <option value="PPL">PPL</option>
                                    <option value="CPL">CPL</option>
                                    <option value="IR">IR</option>
                                    <option value="ME">ME</option>
                                    <option value="FI">FI</option>
                                    <option value="GI">GI</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="atoc_attachment" class="form-label">ATOC Attachment</label>
                                <input type="file" class="form-control" id="atoc_attachment"
                                    name="atoc_attachment" required>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save School</button>
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
        const trainingTableElement = document.getElementById('trainingTable');
        let trainingDataTable;

        function initTrainingDataTable() {
            if (!trainingTableElement || !window.jQuery || !window.jQuery.fn.DataTable) {
                return;
            }

            if (trainingDataTable) {
                trainingDataTable.destroy();
            }

            trainingDataTable = window.jQuery(trainingTableElement).DataTable({
                pageLength: 10,
                order: [
                    [4, 'desc']
                ],
                autoWidth: false,
                columnDefs: [{
                    targets: [5],
                    orderable: false,
                    searchable: false
                }],
            });
        }


        initTrainingDataTable();
    </script>
</body>

</html>
