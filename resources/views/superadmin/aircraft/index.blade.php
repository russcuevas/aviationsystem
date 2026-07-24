<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* Status styling override */
        .status-available {
            background-color: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
        }
        .status-maintenance {
            background-color: rgba(241, 196, 15, 0.15);
            color: #f1c40f;
        }
        .status-unavailable {
            background-color: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
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
            <h2>Aircraft</h2>
            <p>Manage aircraft records, fleet usage, and availability.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-grid-1x2-fill"></i>
                Overview
                <i class="bi bi-chevron-right"></i>
                <span>Aircraft</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: var(--radius);">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: var(--radius);">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- ── Recent Training Progress Table ── -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <p class="panel-title">Aircraft Registry</p>
                    <p class="panel-subtitle">Search and sort fleet records.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#addAircraftModal">
                    <i class="bi bi-plus-lg"></i>
                    Add Aircraft
                </button>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table" id="trainingTable">
                    <thead>
                        <tr>
                            <th>Registration</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Training Provider</th>
                            <th>Total Hours</th>
                            <th>Hours to Overhaul</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aircrafts as $aircraft)
                        <tr data-id="{{ $aircraft->id }}">
                            <td>
                                <div class="school-code-wrap">
                                    <span class="school-code">{{ $aircraft->registration }}</span>
                                </div>
                            </td>
                            <td>{{ $aircraft->type }}</td>
                            <td>{{ $aircraft->model }}</td>
                            <td>
                                <span class="branch-pill">
                                    <i class="bi bi-geo-alt-fill" style="font-size:0.65rem"></i>
                                    {{ $aircraft->provider_name }}
                                </span>
                            </td>
                            <td>{{ number_format($aircraft->total_hours, 1) }} hrs</td>
                            <td>{{ number_format($aircraft->hours_to_overhaul, 1) }} hrs</td>
                            <td>
                                <span class="school-status status-{{ strtolower($aircraft->status) }}">{{ $aircraft->status }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary btn-view-aircraft" 
                                        data-registration="{{ $aircraft->registration }}"
                                        data-type="{{ $aircraft->type }}"
                                        data-model="{{ $aircraft->model }}"
                                        data-total-hours="{{ $aircraft->total_hours }}"
                                        data-hours-to-overhaul="{{ $aircraft->hours_to_overhaul }}"
                                        data-provider="{{ $aircraft->provider_name }}"
                                        data-remarks="{{ $aircraft->remarks }}"
                                        data-status="{{ $aircraft->status }}"
                                        data-documents="{{ json_encode($aircraft->documents) }}"
                                        title="View"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-warning btn-edit-aircraft" 
                                        data-id="{{ $aircraft->id }}"
                                        data-registration="{{ $aircraft->registration }}"
                                        data-type="{{ $aircraft->type }}"
                                        data-model="{{ $aircraft->model }}"
                                        data-total-hours="{{ $aircraft->total_hours }}"
                                        data-hours-to-overhaul="{{ $aircraft->hours_to_overhaul }}"
                                        data-flying-id="{{ $aircraft->flying_id }}"
                                        data-remarks="{{ $aircraft->remarks }}"
                                        data-status="{{ $aircraft->status }}"
                                        data-documents="{{ json_encode($aircraft->documents) }}"
                                        title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete-aircraft" 
                                        data-id="{{ $aircraft->id }}" 
                                        title="Delete"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Add Aircraft Modal -->
    <div class="modal fade" id="addAircraftModal" tabindex="-1" aria-labelledby="addAircraftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAircraftModalLabel">Add Aircraft</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addAircraftForm" action="{{ route('superadmin.aircraft.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="registration" class="form-label">Registration</label>
                                <input type="text" class="form-control" id="registration" name="registration" required placeholder="e.g. RP-C1721">
                            </div>
                            <div class="col-md-4">
                                <label for="aircraftType" class="form-label">Type</label>
                                <input type="text" class="form-control" id="aircraftType" name="aircraftType" required placeholder="e.g. Single Engine">
                            </div>
                            <div class="col-md-4">
                                <label for="aircraftModel" class="form-label">Model</label>
                                <input type="text" class="form-control" id="aircraftModel" name="aircraftModel" required placeholder="e.g. Cessna 172S">
                            </div>

                            <div class="col-md-4">
                                <label for="totalHours" class="form-label">Total Hours</label>
                                <input type="number" class="form-control" id="totalHours" name="totalHours" min="0" step="0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="hoursToOverhaul" class="form-label">Hours to Overhaul (Remaining time)</label>
                                <input type="number" class="form-control" id="hoursToOverhaul" name="hoursToOverhaul" min="0" step="0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="flyingSchool" class="form-label">Training Provider</label>
                                <select class="form-select" id="flyingSchool" name="flyingSchool" required>
                                    <option value="" selected disabled>Select training provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label for="remarks" class="form-label">Notes / Remarks</label>
                                <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Add notes about maintenance, restrictions or remarks.">
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Available" selected>Available</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Unavailable">Unavailable</option>
                                </select>
                            </div>

                            <!-- Certificates & Documents Section -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Aircraft Certificates & Document Expirations
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="addAttachmentRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Custom Document
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table" id="attachmentsTable" style="font-size: 0.85rem;">
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
                                            <!-- Dynamically added rows will appear here -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-text mt-1 text-muted">
                                    <i class="bi bi-info-circle-fill me-1"></i> Please specify the expiration date and upload a scanned file (PDF, JPG, PNG) for Airworthiness, Registration, Radio, Weight/Balance, and Insurance documents.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Aircraft</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Aircraft Modal -->
    <div class="modal fade" id="editAircraftModal" tabindex="-1" aria-labelledby="editAircraftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAircraftModalLabel">Edit Aircraft</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAircraftForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editAircraftId" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editRegistration" class="form-label">Registration</label>
                                <input type="text" class="form-control" id="editRegistration" name="registration" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editAircraftType" class="form-label">Type</label>
                                <input type="text" class="form-control" id="editAircraftType" name="aircraftType" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editAircraftModel" class="form-label">Model</label>
                                <input type="text" class="form-control" id="editAircraftModel" name="aircraftModel" required>
                            </div>

                            <div class="col-md-4">
                                <label for="editTotalHours" class="form-label">Total Hours</label>
                                <input type="number" class="form-control" id="editTotalHours" name="totalHours" min="0" step="0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editHoursToOverhaul" class="form-label">Hours to Overhaul (Remaining time)</label>
                                <input type="number" class="form-control" id="editHoursToOverhaul" name="hoursToOverhaul" min="0" step="0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editFlyingSchool" class="form-label">Training Provider</label>
                                <select class="form-select" id="editFlyingSchool" name="flyingSchool" required>
                                    <option value="" disabled>Select training provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label for="editRemarks" class="form-label">Notes / Remarks</label>
                                <input type="text" class="form-control" id="editRemarks" name="remarks">
                            </div>
                            <div class="col-md-4">
                                <label for="editStatus" class="form-label">Status</label>
                                <select class="form-select" id="editStatus" name="status" required>
                                    <option value="Available">Available</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Unavailable">Unavailable</option>
                                </select>
                            </div>

                            <!-- Current Documents Area -->
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold text-success"><i class="bi bi-file-earmark-check-fill me-1"></i> Current Documents</label>
                                <div class="table-responsive border rounded bg-light-subtle p-2 mb-3">
                                    <table class="table table-sm table-hover align-middle mb-0" id="editExistingLicensesTable" style="font-size: 0.85rem;">
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
                                            <!-- Existing aircraft documents populated in JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="deleted_licenses_container"></div>
                            </div>

                            <!-- New Attachments Area -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-file-earmark-plus-fill me-1"></i> Add New Aircraft Certificates & Document Attachments
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="editAddAttachmentRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Custom Document
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table" id="editAttachmentsTable" style="font-size: 0.85rem;">
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
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Aircraft</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Aircraft Modal -->
    <div class="modal fade" id="viewAircraftModal" tabindex="-1" aria-labelledby="viewAircraftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAircraftModalLabel">Aircraft Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Registration Number</label>
                        <h4 id="viewAircraftRegistration" class="text-primary fw-semibold"></h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Type</label>
                            <div id="viewAircraftType" class="fw-medium"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Model</label>
                            <div id="viewAircraftModel" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Training Provider</label>
                            <div id="viewAircraftProvider" class="fw-medium"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Status</label>
                            <div><span id="viewAircraftStatus" class="school-status"></span></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Total Hours</label>
                            <div id="viewAircraftTotalHours" class="fw-medium"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Hours to Overhaul (Remaining time)</label>
                            <div id="viewAircraftOverhaul" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Notes / Remarks</label>
                        <div id="viewAircraftRemarks" class="p-2 border rounded bg-light-subtle"></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Aircraft Certificates & Document Attachments</label>
                        <div id="viewAircraftDocumentsList" class="list-group">
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
                    [0, 'asc']
                ],
                autoWidth: false,
                columnDefs: [{
                    targets: [7],
                    orderable: false,
                    searchable: false
                }],
            });
        }

        initTrainingDataTable();

        // --- ATTACHMENTS & LICENSES DYNAMIC UI ---
        let addRowCounter = 0;
        let editRowCounter = 0;

        function createAttachmentRow(type = '', number = '', expiry = '', formType = 'add') {
            const counter = formType === 'add' ? ++addRowCounter : ++editRowCounter;
            const inputPrefix = formType === 'add' ? 'attachments' : 'attachments';
            const rowId = `${formType}_row_${counter}`;

            const options = [{
                    value: 'Airworthiness Certificate',
                    text: 'Airworthiness Certificate'
                },
                {
                    value: 'Registration Certificate',
                    text: 'Registration Certificate'
                },
                {
                    value: 'Radio License',
                    text: 'Radio License'
                },
                {
                    value: 'Weight and Balance',
                    text: 'Weight and Balance'
                },
                {
                    value: 'Aircraft Insurance',
                    text: 'Aircraft Insurance'
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
                    <input type="text" class="form-control form-control-sm" name="${inputPrefix}[${counter}][number]" placeholder="e.g. DOC-12345" value="${number}">
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
            addRowCounter = 0;

            const defaultLicenses = [
                { type: 'Airworthiness Certificate' },
                { type: 'Registration Certificate' },
                { type: 'Radio License' },
                { type: 'Weight and Balance' },
                { type: 'Aircraft Insurance' }
            ];

            defaultLicenses.forEach(license => {
                const tr = createAttachmentRow(license.type, '', '', 'add');
                tr.querySelector('input[type="file"]').removeAttribute('required');
                tbody.appendChild(tr);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            resetAddAttachmentsTable();

            const addBtn = document.getElementById('addAttachmentRowBtn');
            const editAddBtn = document.getElementById('editAddAttachmentRowBtn');
            const tbody = document.getElementById('attachmentsTableBody');
            const editTbody = document.getElementById('editAttachmentsTableBody');
            const form = document.getElementById('addAircraftForm');

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

            // Document type change to show/hide custom name input helper
            function handleDocTypeChange(e) {
                if (e.target.classList.contains('document-type-select')) {
                    const select = e.target;
                    const customContainer = select.closest('td').querySelector('.custom-document-type-container');
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

            // Row removal helper
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

            if (tbody) {
                tbody.addEventListener('change', handleDocTypeChange);
                tbody.addEventListener('click', handleRowRemoval);
            }

            if (editTbody) {
                editTbody.addEventListener('change', handleDocTypeChange);
                editTbody.addEventListener('click', handleRowRemoval);
            }

            if (form) {
                form.addEventListener('reset', function() {
                    setTimeout(resetAddAttachmentsTable, 0);
                });
            }
        });

        // ================= VIEW AIRCRAFT DETAILS =================
        $(document).on('click', '.btn-view-aircraft', function() {
            const btn = $(this);
            $('#viewAircraftRegistration').text(btn.data('registration'));
            $('#viewAircraftType').text(btn.data('type'));
            $('#viewAircraftModel').text(btn.data('model'));
            $('#viewAircraftProvider').text(btn.data('provider'));
            
            const status = btn.data('status');
            $('#viewAircraftStatus').text(status)
                .removeClass()
                .addClass('school-status status-' + status.toLowerCase());

            $('#viewAircraftTotalHours').text(parseFloat(btn.data('total-hours')).toFixed(1) + ' hrs');
            $('#viewAircraftOverhaul').text(parseFloat(btn.data('hours-to-overhaul')).toFixed(1) + ' hrs');
            $('#viewAircraftRemarks').text(btn.data('remarks') || 'No remarks provided.');
            
            const documentsList = $('#viewAircraftDocumentsList');
            documentsList.empty();
            
            let documents = [];
            try {
                documents = btn.data('documents');
            } catch(e) {}
            
            if (documents && documents.length > 0) {
                documents.forEach(doc => {
                    const fileName = doc.attachment.split('/').pop().substring(11);
                    documentsList.append(`
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-semibold">${doc.document_type}</h6>
                                <p class="mb-0 text-muted small">No: ${doc.doc_no || 'N/A'} | Expires: ${doc.expiration_date || 'N/A'}</p>
                            </div>
                            <a href="/${doc.attachment}" target="_blank" class="btn btn-sm btn-primary rounded-pill">
                                <i class="bi bi-file-earmark-arrow-down-fill"></i> View
                            </a>
                        </div>
                    `);
                });
            } else {
                documentsList.append('<div class="text-muted small py-2">No uploaded documents or certificates found.</div>');
            }
            
            $('#viewAircraftModal').modal('show');
        });

        // ================= EDIT AIRCRAFT DETAILS =================
        let deletedLicenses = [];

        $(document).on('click', '.btn-edit-aircraft', function() {
            const btn = $(this);
            const id = btn.data('id');
            
            // Set action endpoint
            document.getElementById('editAircraftForm').action = `/superadmin/aircraft/${id}/update`;
            
            $('#editAircraftId').val(id);
            $('#editRegistration').val(btn.data('registration'));
            $('#editAircraftType').val(btn.data('type'));
            $('#editAircraftModel').val(btn.data('model'));
            $('#editTotalHours').val(btn.data('total-hours'));
            $('#editHoursToOverhaul').val(btn.data('hours-to-overhaul'));
            $('#editFlyingSchool').val(btn.data('flying-id'));
            $('#editRemarks').val(btn.data('remarks'));
            $('#editStatus').val(btn.data('status'));
            
            // Clear new attachments rows and deleted files array
            editRowCounter = 0;
            deletedLicenses = [];
            $('#editAttachmentsTableBody').empty();
            $('#deleted_licenses_container').empty();
            
            // Populate existing documents
            const existingTbody = $('#editExistingLicensesTableBody');
            existingTbody.empty();
            
            let documents = [];
            try {
                documents = btn.data('documents');
            } catch(e) {}
            
            if (documents && documents.length > 0) {
                documents.forEach(doc => {
                    const fileName = doc.attachment.split('/').pop().substring(11);
                    existingTbody.append(`
                        <tr data-lic-id="${doc.id}">
                            <td class="fw-semibold">${doc.document_type}</td>
                            <td>${doc.doc_no || 'N/A'}</td>
                            <td>${doc.expiration_date || 'N/A'}</td>
                            <td>
                                <a href="/${doc.attachment}" target="_blank" class="text-decoration-none text-primary">
                                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i>${fileName}
                                </a>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-existing-license" data-lic-id="${doc.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                existingTbody.append('<tr><td colspan="5" class="text-center text-muted py-2">No documents found.</td></tr>');
            }
            
            $('#editAircraftModal').modal('show');
        });

        // Handle existing document removal inside Edit modal
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
                $('#editExistingLicensesTableBody').append('<tr><td colspan="5" class="text-center text-muted py-2">No documents found.</td></tr>');
            }
        });

        // ================= DELETE AIRCRAFT =================
        $(document).on('click', '.btn-delete-aircraft', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this Aircraft record? All documents and uploaded attachments will be permanently deleted.')) {
                const form = document.getElementById('globalDeleteForm');
                form.action = `/superadmin/aircraft/${id}`;
                form.submit();
            }
        });
    </script>
</body>

</html>
