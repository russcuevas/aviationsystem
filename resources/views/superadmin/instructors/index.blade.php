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
            <h2>Instructors</h2>
            <p>Manage instructor records and certification status.</p>
            <div class="page-breadcrumb">
                <i class="bi bi-grid-1x2-fill"></i>
                Overview
                <i class="bi bi-chevron-right"></i>
                <span>Instructors</span>
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
                    <p class="panel-title">Instructor Registry</p>
                    <p class="panel-subtitle">Search and sort instructor records.</p>
                </div>
                <button class="btn-add-form" type="button" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                    <i class="bi bi-plus-lg"></i>
                    Add Instructor
                </button>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table" id="trainingTable">
                    <thead>
                        <tr>
                            <th>Instructor ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Training Provider</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instructors as $instructor)
                        <tr data-id="{{ $instructor->id }}">
                            <td>
                                <div class="school-code-wrap">
                                    <span class="school-code">INS-{{ date('Y', strtotime($instructor->created_at)) }}-{{ sprintf('%03d', $instructor->id) }}</span>
                                </div>
                            </td>
                            <td>{{ $instructor->first_name }} {{ $instructor->middle_name ? $instructor->middle_name . ' ' : '' }}{{ $instructor->last_name }}</td>
                            <td><a class="school-email" href="mailto:{{ $instructor->email }}">{{ $instructor->email }}</a></td>
                            <td>
                                <span class="branch-pill">
                                    <i class="bi bi-geo-alt-fill" style="font-size:0.65rem"></i>
                                    {{ $instructor->provider_name }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary btn-view-instructor" 
                                        data-first-name="{{ $instructor->first_name }}"
                                        data-middle-name="{{ $instructor->middle_name }}"
                                        data-last-name="{{ $instructor->last_name }}"
                                        data-email="{{ $instructor->email }}"
                                        data-phone="{{ $instructor->phone }}"
                                        data-provider="{{ $instructor->provider_name }}"
                                        data-licenses="{{ json_encode($instructor->licenses) }}"
                                        title="View"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-warning btn-edit-instructor" 
                                        data-id="{{ $instructor->id }}"
                                        data-first-name="{{ $instructor->first_name }}"
                                        data-middle-name="{{ $instructor->middle_name }}"
                                        data-last-name="{{ $instructor->last_name }}"
                                        data-email="{{ $instructor->email }}"
                                        data-phone="{{ $instructor->phone }}"
                                        data-flying-id="{{ $instructor->flying_id }}"
                                        data-licenses="{{ json_encode($instructor->licenses) }}"
                                        title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete-instructor" 
                                        data-id="{{ $instructor->id }}" 
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

    <!-- Add Instructor Modal -->
    <div class="modal fade" id="addInstructorModal" tabindex="-1" aria-labelledby="addInstructorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInstructorModalLabel">Add Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addInstructorForm" action="{{ route('superadmin.instructor.store') }}" method="POST" enctype="multipart/form-data">
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
                                <label for="instructorEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="instructorEmail" name="instructorEmail" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6" placeholder="Minimum 6 characters">
                            </div>
                            <div class="col-md-6">
                                <label for="instructorPhone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="instructorPhone" name="instructorPhone" required>
                            </div>

                            <div class="col-md-6">
                                <label for="flyingSchool" class="form-label">Training Provider</label>
                                <select class="form-select" id="flyingSchool" name="flyingSchool" required>
                                    <option value="" selected disabled>Select training provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Licenses & Attachments Section -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Licenses & Document Attachments
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="addAttachmentRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Custom Document
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table" id="attachmentsTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">License / Document Type</th>
                                                <th style="width: 20%;">License/Doc No.</th>
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
                                    <i class="bi bi-info-circle-fill me-1"></i> Please specify the expiration date and upload a scanned file (PDF, JPG, PNG) for each license.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Instructor Modal -->
    <div class="modal fade" id="editInstructorModal" tabindex="-1" aria-labelledby="editInstructorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInstructorModalLabel">Edit Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editInstructorForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editInstructorId" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="editFirstName" name="firstName" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editMiddleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="editMiddleName" name="middleName">
                            </div>
                            <div class="col-md-4">
                                <label for="editLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="editLastName" name="lastName" required>
                            </div>

                            <div class="col-md-6">
                                <label for="editInstructorEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editInstructorEmail" name="instructorEmail" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current password" minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label for="editInstructorPhone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="editInstructorPhone" name="instructorPhone" required>
                            </div>

                            <div class="col-md-6">
                                <label for="editFlyingSchool" class="form-label">Training Provider</label>
                                <select class="form-select" id="editFlyingSchool" name="flyingSchool" required>
                                    <option value="" disabled>Select training provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
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
                                            <!-- Existing instructor documents populated in JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="deleted_licenses_container"></div>
                            </div>

                            <!-- New Attachments Area -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-file-earmark-plus-fill me-1"></i> Add New Licenses & Document Attachments
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="editAddAttachmentRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Custom Document
                                    </button>
                                </div>
                                <div class="table-responsive border rounded bg-light-subtle p-2">
                                    <table class="table table-sm table-hover align-middle mb-0 attachments-table" id="editAttachmentsTable" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">License / Document Type</th>
                                                <th style="width: 20%;">License/Doc No.</th>
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
                        <button type="submit" class="btn btn-primary">Update Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Instructor Modal -->
    <div class="modal fade" id="viewInstructorModal" tabindex="-1" aria-labelledby="viewInstructorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewInstructorModalLabel">Instructor Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Full Name</label>
                        <h5 id="viewInstructorName" class="text-primary fw-semibold"></h5>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Email</label>
                            <div><a id="viewInstructorEmail" href=""></a></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Phone</label>
                            <div id="viewInstructorPhone" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Training Provider</label>
                            <div id="viewInstructorProvider" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">License & Document Attachments</label>
                        <div id="viewInstructorLicensesList" class="list-group">
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
                    targets: [4],
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
                    value: 'Medical Certificate',
                    text: 'Medical Certificate'
                },
                {
                    value: 'NTC License',
                    text: 'NTC License'
                },
                {
                    value: 'Pilot License',
                    text: 'Pilot License'
                },
                {
                    value: 'ELP Certificate',
                    text: 'ELP Certificate'
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
                    <input type="text" class="form-control form-control-sm" name="${inputPrefix}[${counter}][number]" placeholder="e.g. LIC-12345" value="${number}">
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
                { type: 'Medical Certificate' },
                { type: 'NTC License' },
                { type: 'Pilot License' },
                { type: 'ELP Certificate' }
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
            const form = document.getElementById('addInstructorForm');

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

        // ================= VIEW INSTRUCTOR DETAILS =================
        $(document).on('click', '.btn-view-instructor', function() {
            const btn = $(this);
            const fullName = btn.data('first-name') + ' ' + (btn.data('middle-name') ? btn.data('middle-name') + ' ' : '') + btn.data('last-name');
            $('#viewInstructorName').text(fullName);
            $('#viewInstructorEmail').text(btn.data('email')).attr('href', 'mailto:' + btn.data('email'));
            $('#viewInstructorPhone').text(btn.data('phone'));
            $('#viewInstructorProvider').text(btn.data('provider'));
            
            const licensesList = $('#viewInstructorLicensesList');
            licensesList.empty();
            
            let licenses = [];
            try {
                licenses = btn.data('licenses');
            } catch(e) {}
            
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
                licensesList.append('<div class="text-muted small py-2">No uploaded documents or licenses found.</div>');
            }
            
            $('#viewInstructorModal').modal('show');
        });

        // ================= EDIT INSTRUCTOR DETAILS =================
        let deletedLicenses = [];

        $(document).on('click', '.btn-edit-instructor', function() {
            const btn = $(this);
            const id = btn.data('id');
            
            // Set action endpoint
            document.getElementById('editInstructorForm').action = `/superadmin/instructors/${id}/update`;
            
            $('#editInstructorId').val(id);
            $('#editFirstName').val(btn.data('first-name'));
            $('#editMiddleName').val(btn.data('middle-name'));
            $('#editLastName').val(btn.data('last-name'));
            $('#editInstructorEmail').val(btn.data('email'));
            $('#editPassword').val('');
            $('#editInstructorPhone').val(btn.data('phone'));
            $('#editFlyingSchool').val(btn.data('flying-id'));
            
            // Clear new attachments rows and deleted files array
            editRowCounter = 0;
            deletedLicenses = [];
            $('#editAttachmentsTableBody').empty();
            $('#deleted_licenses_container').empty();
            
            // Populate existing licenses
            const existingTbody = $('#editExistingLicensesTableBody');
            existingTbody.empty();
            
            let licenses = [];
            try {
                licenses = btn.data('licenses');
            } catch(e) {}
            
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
                existingTbody.append('<tr><td colspan="5" class="text-center text-muted py-2">No documents found.</td></tr>');
            }
            
            $('#editInstructorModal').modal('show');
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
                $('#editExistingLicensesTableBody').append('<tr><td colspan="5" class="text-center text-muted py-2">No documents found.</td></tr>');
            }
        });

        // ================= DELETE INSTRUCTOR =================
        $(document).on('click', '.btn-delete-instructor', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this Instructor record? All licenses and uploaded attachments will be permanently deleted.')) {
                const form = document.getElementById('globalDeleteForm');
                form.action = `/superadmin/instructors/${id}`;
                form.submit();
            }
        });
    </script>
</body>

</html>
