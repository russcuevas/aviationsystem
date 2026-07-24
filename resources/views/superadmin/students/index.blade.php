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
    <style>
        .attachments-table th {
            font-weight: 600;
            background-color: #f8f9fa;
            color: #212529;
            border-bottom: 2px solid #dee2e6;
            padding: 8px 6px;
        }

        .dark-mode .attachments-table th {
            background-color: #1e293b;
            color: #f1f5f9;
            border-bottom: 2px solid #334155;
        }

        .attachments-table td {
            padding: 8px 6px;
            vertical-align: middle;
        }

        .attachments-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f52ba !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .dark-mode .attachments-section-title {
            color: #38bdf8 !important;
        }

        .dark-mode .table-responsive {
            border-color: #334155 !important;
            background-color: #0f172a !important;
        }

        .dark-mode .attachments-table td {
            border-color: #334155 !important;
        }

        .dark-mode .form-text {
            color: #94a3b8 !important;
        }

        .btn-xs {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
    </style>
</head>

<body>

    <!-- ================= SIDEBAR ================= -->
    @include('superadmin.components.left_sidebar')

    <!-- ================= TOPBAR ================= -->
    @include('superadmin.components.topbar')

    <!-- ─── MAIN CONTENT ─── -->
    <main class="main-content">

        <!-- Page Header -->
        <div class="page-header">
            <h2>Students</h2>
            <p>Manage student pilot records, course registrations, and stages progress.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-mortarboard-fill"></i>
                Overview
                <i class="bi bi-chevron-right"></i>
                <span>Students</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert"
                style="border-radius: var(--radius);">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert"
                style="border-radius: var(--radius);">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Recent Table Panel -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Student Registry</p>
                    <p class="panel-subtitle">Search and sort student records.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-plus-lg"></i>
                    Add Student
                </button>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table" id="trainingTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Training Provider</th>
                            <th>Active Stages</th>
                            <th>Required Hours</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr data-id="{{ $student->id }}">
                                <td>
                                    <div class="school-code-wrap">
                                        <span class="school-code">STU-{{ date('Y', strtotime($student->created_at)) }}-{{ sprintf('%03d', $student->id) }}</span>
                                    </div>
                                </td>
                                <td>{{ $student->first_name }}
                                    {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}
                                </td>
                                <td><a class="school-email"
                                        href="mailto:{{ $student->email }}">{{ $student->email }}</a></td>
                                <td>
                                    <span class="branch-pill">
                                        <i class="bi bi-geo-alt-fill" style="font-size:0.65rem"></i>
                                        {{ $student->provider_name }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($student->stages as $stg)
                                        <div class="mb-1">
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size: 0.72rem;">
                                                {{ $stg->stage }} 
                                                <span class="badge bg-primary ms-1" style="font-size: 0.62rem; line-height: 1;">{{ $stg->status }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($student->stages as $stg)
                                        <div class="mb-1" style="font-size: 0.82rem; font-weight: 500;">{{ $stg->required_hours }} hrs</div>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary btn-view-student"
                                            data-first-name="{{ $student->first_name }}"
                                            data-middle-name="{{ $student->middle_name }}"
                                            data-last-name="{{ $student->last_name }}"
                                            data-email="{{ $student->email }}" data-phone="{{ $student->phone }}"
                                            data-dob="{{ $student->date_of_birth }}"
                                            data-enrollment="{{ $student->enrollment_date }}"
                                            data-provider="{{ $student->provider_name }}"
                                            data-stages="{{ json_encode($student->stages) }}"
                                            data-licenses="{{ json_encode($student->licenses) }}" title="View"><i
                                                class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning btn-edit-student"
                                            data-id="{{ $student->id }}" data-first-name="{{ $student->first_name }}"
                                            data-middle-name="{{ $student->middle_name }}"
                                            data-last-name="{{ $student->last_name }}"
                                            data-email="{{ $student->email }}" data-phone="{{ $student->phone }}"
                                            data-dob="{{ $student->date_of_birth }}"
                                            data-enrollment="{{ $student->enrollment_date }}"
                                            data-flying-id="{{ $student->flying_id }}"
                                            data-stages="{{ json_encode($student->stages) }}"
                                            data-licenses="{{ json_encode($student->licenses) }}" title="Edit"><i
                                                class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-student"
                                            data-id="{{ $student->id }}" title="Delete"><i
                                                class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addStudentForm" action="{{ route('superadmin.student.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                            </div>
                            <div class="col-md-4">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middleName">
                            </div>
                            <div class="col-md-4">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>

                            <div class="col-md-6">
                                <label for="studentEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="studentEmail" name="studentEmail"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    minlength="6" placeholder="Minimum 6 characters">
                            </div>
                            <div class="col-md-6">
                                <label for="studentPhone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="studentPhone" name="studentPhone"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="enrollmentDate" class="form-label">Enrollment Date</label>
                                <input type="date" class="form-control" id="enrollmentDate" name="enrollmentDate"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="flyingSchool" class="form-label">Training Provider</label>
                                <select class="form-select" id="flyingSchool" name="flyingSchool" required>
                                    <option value="" selected disabled>Select training provider</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Staging Configurations Section -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-graph-up-arrow me-1"></i> Staging Configurations (Bulk)
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="addStageRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Stage
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table" id="stagingTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th style="width: 45%;">Stage Name</th>
                                                <th style="width: 25%;">Required Hours</th>
                                                <th style="width: 25%;">Status</th>
                                                <th style="width: 5%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stagingTableBody">
                                            <!-- Dynamically added staging rows appear here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Licenses & Documents Section -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Student Licenses & Scanned Attachments
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center"
                                        id="addAttachmentRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Document
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table"
                                        id="attachmentsTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Document Type</th>
                                                <th style="width: 20%;">Document No.</th>
                                                <th style="width: 20%;">Expiration Date</th>
                                                <th style="width: 25%;">Upload Attachment (Scanned)</th>
                                                <th style="width: 5%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attachmentsTableBody">
                                            <!-- Dynamically added rows appear here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editStudentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editStudentId" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="editFirstName" name="firstName"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="editMiddleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="editMiddleName" name="middleName">
                            </div>
                            <div class="col-md-4">
                                <label for="editLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="editLastName" name="lastName"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="editStudentEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editStudentEmail" name="studentEmail"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="editPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="editPassword" name="password"
                                    placeholder="Leave blank to keep current password" minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label for="editStudentPhone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="editStudentPhone" name="studentPhone"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="editDateOfBirth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="editDateOfBirth" name="dateOfBirth"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="editEnrollmentDate" class="form-label">Enrollment Date</label>
                                <input type="date" class="form-control" id="editEnrollmentDate"
                                    name="enrollmentDate" required>
                            </div>

                            <div class="col-md-6">
                                <label for="editFlyingSchool" class="form-label">Training Provider</label>
                                <select class="form-select" id="editFlyingSchool" name="flyingSchool" required>
                                    <option value="" disabled>Select training provider</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Current Stages Area -->
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold text-success"><i class="bi bi-graph-up-arrow me-1"></i> Current Stages</label>
                                <div class="table-responsive border rounded bg-light-subtle p-2 mb-3">
                                    <table class="table table-sm table-hover align-middle mb-0" id="editExistingStagesTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th>Stage Name</th>
                                                <th>Required Hours</th>
                                                <th>Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="editExistingStagesTableBody">
                                            <!-- Existing stages populated in JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="deleted_stages_container"></div>
                            </div>

                            <!-- Add New Stages -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-plus-lg me-1"></i> Add New Staging Configurations
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="editAddStageRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Stage
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table" id="editStagesTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th style="width: 45%;">Stage Name</th>
                                                <th style="width: 25%;">Required Hours</th>
                                                <th style="width: 25%;">Status</th>
                                                <th style="width: 5%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="editStagesTableBody">
                                            <!-- Dynamic new stages appended in JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Current Documents Area -->
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold text-success"><i
                                        class="bi bi-file-earmark-check-fill me-1"></i> Current Documents</label>
                                <div class="table-responsive border rounded bg-light-subtle p-2 mb-3">
                                    <table class="table table-sm table-hover align-middle mb-0"
                                        id="editExistingLicensesTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th>Document Type</th>
                                                <th>Doc No.</th>
                                                <th>Expiration Date</th>
                                                <th>Attachment</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="editExistingLicensesTableBody">
                                            <!-- Existing documents populated in JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="deleted_licenses_container"></div>
                            </div>

                            <!-- New Attachments Area -->
                            <div class="col-12">
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-file-earmark-plus-fill me-1"></i> Add New Student Licenses & Document Attachments
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center"
                                        id="editAddAttachmentRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Document
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table"
                                        id="editAttachmentsTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Document Type</th>
                                                <th style="width: 20%;">Document No.</th>
                                                <th style="width: 20%;">Expiration Date</th>
                                                <th style="width: 25%;">Upload Attachment (Scanned)</th>
                                                <th style="width: 5%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="editAttachmentsTableBody">
                                            <!-- Dynamic new documents inputs appended in JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Student Modal -->
    <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Full Name</label>
                        <h5 id="viewStudentName" class="text-primary fw-semibold"></h5>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Email</label>
                            <div><a id="viewStudentEmail" href=""></a></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Phone</label>
                            <div id="viewStudentPhone" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Date of Birth</label>
                            <div id="viewStudentDob" class="fw-medium"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Enrollment Date</label>
                            <div id="viewStudentEnrollment" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Training Provider</label>
                            <div id="viewStudentProvider" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="mb-3 border-top pt-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Staging Configurations & Progress</label>
                        <div id="viewStudentStagesList" class="list-group">
                            <!-- Populated in JS -->
                        </div>
                    </div>
                    <div class="mb-3 border-top pt-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">License & Document Attachments</label>
                        <div id="viewStudentLicensesList" class="list-group">
                            <!-- Downloadable list populated in JS -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Delete Form -->
    <form id="globalDeleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

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
                    [1, 'asc']
                ],
                autoWidth: false,
                columnDefs: [{
                    targets: [6],
                    orderable: false,
                    searchable: false
                }],
            });
        }

        initTrainingDataTable();

        // --- ATTACHMENTS & LICENSES DYNAMIC UI ---
        let rowCounter = 0;
        let editRowCounter = 0;

        function createAttachmentRow(type = '', number = '', expiry = '', formType = 'add') {
            const counter = formType === 'add' ? ++rowCounter : ++editRowCounter;
            const inputPrefix = formType === 'add' ? 'attachments' : 'attachments';
            const rowId = `${formType}_row_${counter}`;

            const options = [{
                    value: 'Student Pilot License (SPL)',
                    text: 'Student Pilot License (SPL)'
                },
                {
                    value: 'Private Pilot License (PPL)',
                    text: 'Private Pilot License (PPL)'
                },
                {
                    value: 'Commercial Pilot License (CPL)',
                    text: 'Commercial Pilot License (CPL)'
                },
                {
                    value: 'Medical Certificate (Class 1)',
                    text: 'Medical Certificate (Class 1)'
                },
                {
                    value: 'Medical Certificate (Class 2)',
                    text: 'Medical Certificate (Class 2)'
                },
                {
                    value: 'Other',
                    text: 'Other / Custom Document'
                }
            ];

            let selectHtml =
                `<select class="form-select form-select-sm document-type-select" name="${inputPrefix}[${counter}][type]" required>`;
            selectHtml += `<option value="" disabled ${type === '' ? 'selected' : ''}>Select document type</option>`;

            let isMatched = false;
            options.forEach(opt => {
                const isSelected = type === opt.value ? 'selected' : '';
                if (isSelected) isMatched = true;
                selectHtml += `<option value="${opt.value}" ${isSelected}>${opt.text}</option>`;
            });

            if (type !== '' && !isMatched) {
                selectHtml += `<option value="Other" selected>Other / Custom Document</option>`;
            } else if (type === '') {
                selectHtml += `<option value="Other">Other / Custom Document</option>`;
            }
            selectHtml += `</select>`;

            const isOther = (type !== '' && !isMatched) || type === 'Other';
            const customTypeVal = isOther && type !== 'Other' ? type : '';
            const customInputStyle = isOther ? '' : 'display: none;';

            const customInputHtml = `
                <div class="mt-1 custom-document-type-container" style="${customInputStyle}">
                    <input type="text" class="form-control form-control-sm custom-document-type-input" 
                           name="${inputPrefix}[${counter}][custom_type]" 
                           placeholder="Enter document name" 
                           value="${customTypeVal}" 
                           ${isOther ? 'required' : ''}>
                </div>
            `;

            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.innerHTML = `
                <td>
                    ${selectHtml}
                    ${customInputHtml}
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="${inputPrefix}[${counter}][number]" placeholder="e.g. SPL-12345" value="${number}">
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm" name="${inputPrefix}[${counter}][expiration_date]" value="${expiry}">
                </td>
                <td>
                    <input type="file" class="form-control form-control-sm" name="${inputPrefix}[${counter}][file]" accept=".pdf,.jpg,.jpeg,.png" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" data-row-id="${rowId}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            return tr;
        }

        function resetAddAttachmentsTable() {
            const tbody = document.getElementById('attachmentsTableBody');
            if (!tbody) return;
            tbody.innerHTML = '';
            rowCounter = 0;

            const defaultLicenses = [{
                    type: 'Student Pilot License (SPL)'
                },
                {
                    type: 'Medical Certificate (Class 2)'
                }
            ];

            defaultLicenses.forEach(license => {
                const tr = createAttachmentRow(license.type, '', '', 'add');
                tr.querySelector('input[type="file"]').removeAttribute('required');
                tbody.appendChild(tr);
            });
        }

        // --- STAGING CONFIGURATIONS DYNAMIC UI ---
        let stageRowCounter = 0;
        let editStageRowCounter = 0;

        function createStageRow(stage = '', hours = '', status = 'Active', formType = 'add') {
            const counter = formType === 'add' ? ++stageRowCounter : ++editStageRowCounter;
            const inputPrefix = formType === 'add' ? 'stages' : 'stages';
            const rowId = `${formType}_stage_row_${counter}`;

            const options = [
                { value: 'PPL Ground', text: 'PPL Ground' },
                { value: 'PPL Flight', text: 'PPL Flight' },
                { value: 'CPL Ground', text: 'CPL Ground' },
                { value: 'IR Ground', text: 'IR Ground' },
                { value: 'IR Flight', text: 'IR Flight' },
                { value: 'ME Ground', text: 'ME Ground' }
            ];

            let selectHtml = `<select class="form-select form-select-sm" name="${inputPrefix}[${counter}][stage]" required>`;
            selectHtml += `<option value="" disabled ${stage === '' ? 'selected' : ''}>Select stage</option>`;
            options.forEach(opt => {
                selectHtml += `<option value="${opt.value}" ${stage === opt.value ? 'selected' : ''}>${opt.text}</option>`;
            });
            selectHtml += `</select>`;

            const statusHtml = `
                <select class="form-select form-select-sm" name="${inputPrefix}[${counter}][status]" required>
                    <option value="In Progress" ${status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                    <option value="Completed" ${status === 'Completed' ? 'selected' : ''}>Completed</option>
                </select>
            `;

            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.innerHTML = `
                <td>${selectHtml}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="${inputPrefix}[${counter}][required_hours]" min="1" required value="${hours}">
                </td>
                <td>${statusHtml}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-stage-row-btn" data-row-id="${rowId}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            return tr;
        }

        function resetAddStagingTable() {
            const tbody = document.getElementById('stagingTableBody');
            if (!tbody) return;
            tbody.innerHTML = '';
            stageRowCounter = 0;

            // Default starting stage configuration
            tbody.appendChild(createStageRow('PPL Ground', '40', 'In Progress', 'add'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            resetAddAttachmentsTable();
            resetAddStagingTable();

            const addBtn = document.getElementById('addAttachmentRowBtn');
            const editAddBtn = document.getElementById('editAddAttachmentRowBtn');
            const addStageBtn = document.getElementById('addStageRowBtn');
            const editAddStageBtn = document.getElementById('editAddStageRowBtn');

            const tbody = document.getElementById('attachmentsTableBody');
            const editTbody = document.getElementById('editAttachmentsTableBody');
            const stagingTbody = document.getElementById('stagingTableBody');
            const editStagingTbody = document.getElementById('editStagesTableBody');

            const form = document.getElementById('addStudentForm');

            // Attach handlers
            if (addBtn && tbody) {
                addBtn.addEventListener('click', function() {
                    tbody.appendChild(createAttachmentRow('', '', '', 'add'));
                });
            }

            if (editAddBtn && editTbody) {
                editAddBtn.addEventListener('click', function() {
                    editTbody.appendChild(createAttachmentRow('', '', '', 'edit'));
                });
            }

            if (addStageBtn && stagingTbody) {
                addStageBtn.addEventListener('click', function() {
                    stagingTbody.appendChild(createStageRow('', '', 'Active', 'add'));
                });
            }

            if (editAddStageBtn && editStagingTbody) {
                editAddStageBtn.addEventListener('click', function() {
                    editStagingTbody.appendChild(createStageRow('', '', 'Active', 'edit'));
                });
            }

            // Custom type hide/show toggles
            function handleDocTypeChange(e) {
                if (e.target.classList.contains('document-type-select')) {
                    const select = e.target;
                    const customContainer = select.closest('td').querySelector(
                        '.custom-document-type-container');
                    const customInput = customContainer.querySelector('.custom-document-type-input');

                    if (select.value === 'Other') {
                        customContainer.style.display = 'block';
                        customInput.setAttribute('required', 'required');
                        customInput.focus();
                    } else {
                        customContainer.style.display = 'none';
                        customInput.removeAttribute('required');
                        customInput.value = '';
                    }
                }
            }

            // Removing lines handlers
            function handleRowRemoval(e) {
                const btn = e.target.closest('.remove-row-btn');
                if (btn) {
                    const rowId = btn.getAttribute('data-row-id');
                    const row = document.getElementById(rowId);
                    if (row) {
                        row.remove();
                    }
                }
            }

            function handleStageRowRemoval(e) {
                const btn = e.target.closest('.remove-stage-row-btn');
                if (btn) {
                    const rowId = btn.getAttribute('data-row-id');
                    const row = document.getElementById(rowId);
                    if (row) {
                        row.remove();
                    }
                }
            }

            if (tbody) {
                tbody.addEventListener('change', handleDocTypeChange);
                tbody.addEventListener('click', handleRowRemoval);
            }

            if (editTbody) {
                editTbody.addEventListener('change', handleDocTypeChange);
                editTbody.addEventListener('click', handleRowRemoval);
            }

            if (stagingTbody) {
                stagingTbody.addEventListener('click', handleStageRowRemoval);
            }

            if (editStagingTbody) {
                editStagingTbody.addEventListener('click', handleStageRowRemoval);
            }

            if (form) {
                form.addEventListener('reset', function() {
                    setTimeout(resetAddAttachmentsTable, 0);
                    setTimeout(resetAddStagingTable, 0);
                });
            }
        });

        // ================= VIEW STUDENT DETAILS =================
        $(document).on('click', '.btn-view-student', function() {
            const btn = $(this);
            const fullName = btn.data('first-name') + ' ' + (btn.data('middle-name') ? btn.data('middle-name') +
                ' ' : '') + btn.data('last-name');
            $('#viewStudentName').text(fullName);
            $('#viewStudentEmail').text(btn.data('email')).attr('href', 'mailto:' + btn.data('email'));
            $('#viewStudentPhone').text(btn.data('phone'));
            $('#viewStudentDob').text(btn.data('dob'));
            $('#viewStudentEnrollment').text(btn.data('enrollment'));
            $('#viewStudentProvider').text(btn.data('provider'));

            // Populate stages list
            const stagesList = $('#viewStudentStagesList');
            stagesList.empty();

            let stages = [];
            try {
                stages = btn.data('stages');
            } catch(e) {}

            if (stages && stages.length > 0) {
                stages.forEach(stg => {
                    stagesList.append(`
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-semibold">${stg.stage}</h6>
                                <p class="mb-0 text-muted small">Required Hours: ${stg.required_hours} hrs</p>
                            </div>
                            <span class="badge bg-primary rounded-pill px-3">${stg.status}</span>
                        </div>
                    `);
                });
            } else {
                stagesList.append('<div class="text-muted small py-2">No stages configurations found.</div>');
            }

            const licensesList = $('#viewStudentLicensesList');
            licensesList.empty();

            let licenses = [];
            try {
                licenses = btn.data('licenses');
            } catch (e) {}

            if (licenses && licenses.length > 0) {
                licenses.forEach(lic => {
                    const fileName = lic.attachment.split('/').pop().substring(11);
                    licensesList.append(`
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-semibold">${lic.document_type}</h6>
                                <p class="mb-0 text-muted small">No: ${lic.doc_no || 'N/A'} | Expires: ${lic.expiration_date || 'N/A'}</p>
                            </div>
                            <a href="/${lic.attachment}" target="_blank" class="btn btn-sm btn-primary rounded-pill">
                                <i class="bi bi-file-earmark-arrow-down-fill"></i> View
                            </a>
                        </div>
                    `);
                });
            } else {
                licensesList.append(
                    '<div class="text-muted small py-2">No uploaded documents or licenses found.</div>');
            }

            $('#viewStudentModal').modal('show');
        });

        // ================= EDIT STUDENT DETAILS =================
        let deletedLicenses = [];
        let deletedStages = [];

        $(document).on('click', '.btn-edit-student', function() {
            const btn = $(this);
            const id = btn.data('id');

            // Set action endpoint
            document.getElementById('editStudentForm').action = `/superadmin/students/${id}/update`;

            $('#editStudentId').val(id);
            $('#editFirstName').val(btn.data('first-name'));
            $('#editMiddleName').val(btn.data('middle-name'));
            $('#editLastName').val(btn.data('last-name'));
            $('#editStudentEmail').val(btn.data('email'));
            $('#editPassword').val('');
            $('#editStudentPhone').val(btn.data('phone'));
            $('#editDateOfBirth').val(btn.data('dob'));
            $('#editEnrollmentDate').val(btn.data('enrollment'));
            $('#editFlyingSchool').val(btn.data('flying-id'));

            // Clear arrays and dynamic table containers
            editRowCounter = 0;
            editStageRowCounter = 0;
            deletedLicenses = [];
            deletedStages = [];
            
            $('#editStagesTableBody').empty();
            $('#editAttachmentsTableBody').empty();
            
            $('#deleted_licenses_container').empty();
            $('#deleted_stages_container').empty();

            // Populate existing staging records
            const existingStagesTbody = $('#editExistingStagesTableBody');
            existingStagesTbody.empty();

            let stages = [];
            try {
                stages = btn.data('stages');
            } catch(e) {}

            if (stages && stages.length > 0) {
                stages.forEach((stg, index) => {
                    const options = [
                        'PPL Ground', 'PPL Flight', 'CPL Ground', 'IR Ground', 'IR Flight', 'ME Ground'
                    ];
                    let selectHtml = `<select class="form-select form-select-sm" name="existing_stages[${index}][stage]" required>`;
                    options.forEach(opt => {
                        selectHtml += `<option value="${opt}" ${stg.stage === opt ? 'selected' : ''}>${opt}</option>`;
                    });
                    selectHtml += `</select>`;

                    const statusHtml = `
                        <select class="form-select form-select-sm" name="existing_stages[${index}][status]" required>
                            <option value="In Progress" ${stg.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Completed" ${stg.status === 'Completed' ? 'selected' : ''}>Completed</option>
                        </select>
                    `;

                    existingStagesTbody.append(`
                        <tr data-stage-id="${stg.id}">
                            <td>
                                <input type="hidden" name="existing_stages[${index}][id]" value="${stg.id}">
                                ${selectHtml}
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" name="existing_stages[${index}][required_hours]" min="1" required value="${stg.required_hours}">
                            </td>
                            <td>${statusHtml}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-existing-stage" data-stage-id="${stg.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                existingStagesTbody.append('<tr><td colspan="4" class="text-center text-muted py-2">No stages found.</td></tr>');
            }

            // Populate existing licenses
            const existingTbody = $('#editExistingLicensesTableBody');
            existingTbody.empty();

            let licenses = [];
            try {
                licenses = btn.data('licenses');
            } catch (e) {}

            if (licenses && licenses.length > 0) {
                licenses.forEach(lic => {
                    const fileName = lic.attachment.split('/').pop().substring(11);
                    existingTbody.append(`
                        <tr data-lic-id="${lic.id}">
                            <td class="fw-semibold">${lic.document_type}</td>
                            <td>${lic.doc_no || 'N/A'}</td>
                            <td>${lic.expiration_date || 'N/A'}</td>
                            <td>
                                <a href="/${lic.attachment}" target="_blank" class="text-decoration-none text-primary">
                                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i>${fileName}
                                </a>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-existing-license" data-lic-id="${lic.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                existingTbody.append(
                    '<tr><td colspan="5" class="text-center text-muted py-2">No documents found.</td></tr>');
            }

            $('#editStudentModal').modal('show');
        });

        // Handle existing stage removal inside Edit modal
        $(document).on('click', '.btn-remove-existing-stage', function() {
            const btn = $(this);
            const stageId = btn.data('stage-id');
            deletedStages.push(stageId);

            $('#deleted_stages_container').append(`
                <input type="hidden" name="deleted_stages[]" value="${stageId}">
            `);

            btn.closest('tr').remove();

            if ($('#editExistingStagesTableBody').children().length === 0) {
                $('#editExistingStagesTableBody').append('<tr><td colspan="4" class="text-center text-muted py-2">No stages found.</td></tr>');
            }
        });

        // Handle existing license removal inside Edit modal
        $(document).on('click', '.btn-remove-existing-license', function() {
            const btn = $(this);
            const licId = btn.data('lic-id');
            deletedLicenses.push(licId);

            // Add hidden input to form
            $('#deleted_licenses_container').append(`
                <input type="hidden" name="deleted_licenses[]" value="${licId}">
            `);

            // Remove the table row
            btn.closest('tr').remove();

            if ($('#editExistingLicensesTableBody').children().length === 0) {
                $('#editExistingLicensesTableBody').append(
                    '<tr><td colspan="5" class="text-center text-muted py-2">No documents found.</td></tr>');
            }
        });

        // ================= DELETE STUDENT =================
        $(document).on('click', '.btn-delete-student', function() {
            const id = $(this).data('id');
            if (confirm(
                    'Are you sure you want to delete this Student record? All licenses and uploaded attachments will be permanently deleted.'
                )) {
                const form = document.getElementById('globalDeleteForm');
                form.action = `/superadmin/students/${id}`;
                form.submit();
            }
        });
    </script>
</body>

</html>
