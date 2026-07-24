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

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• MAIN CONTENT â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
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

        <!-- â”€â”€ Recent Training Progress Table â”€â”€ -->
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="school-code-wrap">
                                    <span class="school-code">RP-C1721</span>
                                </div>
                            </td>
                            <td>Single Engine</td>
                            <td>Cessna 172S</td>
                            <td><span class="branch-pill"><i class="bi bi-geo-alt-fill"
                                        style="font-size:0.65rem"></i>PhilSCA Villamor</span></td>
                            <td>4,210 hrs</td>
                            <td>180 hrs</td>
                            <td><span class="school-status status-available">Available</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="school-code-wrap"><span class="school-code">RP-DA401</span></div>
                            </td>
                            <td>Twin Engine</td>
                            <td>Diamond DA42</td>
                            <td><span class="branch-pill"><i class="bi bi-geo-alt-fill"
                                        style="font-size:0.65rem"></i>PhilSCA Villamor</span></td>
                            <td>2,044 hrs</td>
                            <td>320 hrs</td>
                            <td><span class="school-status status-maintenance">Maintenance</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <div class="modal fade" id="addAircraftModal" tabindex="-1" aria-labelledby="addAircraftModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAircraftModalLabel">Add Aircraft</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addAircraftForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="registration" class="form-label">Registration</label>
                                <input type="text" class="form-control" id="registration" name="registration"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="aircraftType" class="form-label">Type</label>
                                <input type="text" class="form-control" id="aircraftType" name="aircraftType"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="aircraftModel" class="form-label">Model</label>
                                <input type="text" class="form-control" id="aircraftModel" name="aircraftModel"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label for="totalHours" class="form-label">Total Hours</label>
                                <input type="number" class="form-control" id="totalHours" name="totalHours"
                                    min="0" step="0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="hoursToOverhaul" class="form-label">Hours to Overhaul</label>
                                <input type="number" class="form-control" id="hoursToOverhaul" name="hoursToOverhaul"
                                    min="0" step="0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="aircraftSchool" class="form-label">Training Provider</label>
                                <input type="text" class="form-control" id="aircraftSchool" name="aircraftSchool"
                                    required>
                            </div>

                            <div class="col-12">
                                <label for="aircraftRemarks" class="form-label">Notes/Remarks</label>
                                <textarea class="form-control" id="aircraftRemarks" name="aircraftRemarks" rows="2"
                                    placeholder="Add notes about maintenance condition, restrictions, or remarks."></textarea>
                            </div>

                            <!-- Licenses & Attachments Section -->
                            <div class="col-12 mt-4">
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
                                    <h6 class="fw-bold mb-0 text-primary attachments-section-title">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Aircraft Certificates &
                                        Document Expirations
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary d-flex align-items-center"
                                        id="addAttachmentRowBtn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Custom Document
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
                                            <!-- Dynamically added rows will appear here -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-text mt-1 text-muted">
                                    <i class="bi bi-info-circle-fill me-1"></i> Please specify the expiration date and
                                    upload a scanned file (PDF, JPG, PNG) for each aircraft document.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Aircraft</button>
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
                    [0, 'asc']
                ],
                autoWidth: false,
            });
        }

        initTrainingDataTable();

        // --- ATTACHMENTS & LICENSES DYNAMIC UI ---
        let rowCounter = 0;

        function createAttachmentRow(type = '', number = '', expiry = '') {
            rowCounter++;
            const rowId = `row_${rowCounter}`;

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
                `<select class="form-select form-select-sm document-type-select" name="attachments[${rowCounter}][type]" required>`;
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
                           name="attachments[${rowCounter}][custom_type]" 
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
                    <input type="text" class="form-control form-control-sm" name="attachments[${rowCounter}][number]" placeholder="e.g. DOC-12345" value="${number}">
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm" name="attachments[${rowCounter}][expiration_date]" value="${expiry}">
                </td>
                <td>
                    <input type="file" class="form-control form-control-sm" name="attachments[${rowCounter}][file]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" data-row-id="${rowId}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            return tr;
        }

        function resetAttachmentsTable() {
            const tbody = document.getElementById('attachmentsTableBody');
            if (!tbody) return;
            tbody.innerHTML = '';
            rowCounter = 0;

            const defaultLicenses = [{
                    type: 'Airworthiness Certificate'
                },
                {
                    type: 'Registration Certificate'
                },
                {
                    type: 'Radio License'
                },
                {
                    type: 'Weight and Balance'
                },
                {
                    type: 'Aircraft Insurance'
                }
            ];

            defaultLicenses.forEach(license => {
                tbody.appendChild(createAttachmentRow(license.type));
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            resetAttachmentsTable();

            const addBtn = document.getElementById('addAttachmentRowBtn');
            const tbody = document.getElementById('attachmentsTableBody');
            const form = document.getElementById('addAircraftForm');

            if (addBtn && tbody) {
                addBtn.addEventListener('click', function() {
                    tbody.appendChild(createAttachmentRow());
                });
            }

            if (tbody) {
                // Handle document type change to show/hide custom name input
                tbody.addEventListener('change', function(e) {
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
                });

                // Handle removing a row
                tbody.addEventListener('click', function(e) {
                    const btn = e.target.closest('.remove-row-btn');
                    if (btn) {
                        const rowId = btn.getAttribute('data-row-id');
                        const row = document.getElementById(rowId);
                        if (row) {
                            row.remove();
                        }
                    }
                });
            }

            if (form) {
                form.addEventListener('reset', function() {
                    setTimeout(resetAttachmentsTable, 0);
                });
            }
        });
    </script>
</body>

</html>
