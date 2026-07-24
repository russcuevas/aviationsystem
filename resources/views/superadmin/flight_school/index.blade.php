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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($providers as $provider)
                        <tr data-id="{{ $provider->id }}">
                            <td>
                                <div class="school-code-wrap">
                                    <span class="school-code">{{ $provider->code }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="school-name-wrap">
                                    <span class="school-name">{{ $provider->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="school-address">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>{{ $provider->address }}</span>
                                </div>
                            </td>
                            <td>
                                <a class="school-email"
                                    href="mailto:{{ $provider->email }}">{{ $provider->email }}</a>
                            </td>
                            <td>
                                {{ $provider->accreditation_course }}
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary btn-view-provider" 
                                        data-name="{{ $provider->name }}"
                                        data-code="{{ $provider->code }}"
                                        data-address="{{ $provider->address }}"
                                        data-email="{{ $provider->email }}"
                                        data-phone="{{ $provider->phone }}"
                                        data-course="{{ $provider->accreditation_course }}"
                                        data-attachments="{{ json_encode($provider->atoc_attachment ?? []) }}"
                                        title="View"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-warning btn-edit-provider" 
                                        data-id="{{ $provider->id }}"
                                        data-name="{{ $provider->name }}"
                                        data-code="{{ $provider->code }}"
                                        data-address="{{ $provider->address }}"
                                        data-email="{{ $provider->email }}"
                                        data-phone="{{ $provider->phone }}"
                                        data-course="{{ $provider->accreditation_course }}"
                                        data-attachments="{{ json_encode($provider->atoc_attachment ?? []) }}"
                                        title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete-provider" 
                                        data-id="{{ $provider->id }}" 
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

    <div class="modal fade" id="addSchoolModal" tabindex="-1" aria-labelledby="addSchoolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSchoolModalLabel">Add Training Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSchoolForm" action="{{ route('superadmin.flight.school.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="schoolName" class="form-label">Provider Name</label>
                                <input type="text" class="form-control" id="schoolName" name="schoolName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="schoolCode" class="form-label">Provider Code</label>
                                <input type="text" class="form-control" id="schoolCode" name="schoolCode" required>
                            </div>

                            <div class="col-md-6">
                                <label for="schoolAddress" class="form-label">Address</label>
                                <input type="text" class="form-control" id="schoolAddress" name="schoolAddress" required>
                            </div>

                            <div class="col-md-6">
                                <label for="contactEmail" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contactEmail" name="contactEmail" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contactPhone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="contactPhone" name="contactPhone" required>
                            </div>

                            <div class="col-md-6">
                                <label for="accreditation_course" class="form-label">Accreditation Course</label>
                                <select class="form-select" id="accreditation_course" name="accreditation_course" required>
                                    <option value="" selected disabled>Select accreditation course</option>
                                    <option value="PPL">PPL</option>
                                    <option value="CPL">CPL</option>
                                    <option value="IR">IR</option>
                                    <option value="ME">ME</option>
                                    <option value="FI">FI</option>
                                    <option value="GI">GI</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">ATOC Attachments</label>
                                <div class="file-upload-drag-area border border-dashed rounded-3 p-4 text-center position-relative mb-2" style="border-style: dashed !important; border-color: var(--purple) !important; background-color: var(--cobalt-light);">
                                    <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" id="add_atoc_attachment" name="atoc_attachment[]" multiple style="cursor: pointer;">
                                    <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary"></i>
                                    <p class="mb-1 mt-2 fw-semibold">Drag & Drop files here or click to browse</p>
                                    <p class="text-muted small">Upload multiple files (PDF, images, documents)</p>
                                </div>
                                <div id="add_selected_files_list" class="d-flex flex-wrap gap-2"></div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Provider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit School Modal -->
    <div class="modal fade" id="editSchoolModal" tabindex="-1" aria-labelledby="editSchoolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSchoolModalLabel">Edit Training Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSchoolForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editSchoolId" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="editSchoolName" class="form-label">Provider Name</label>
                                <input type="text" class="form-control" id="editSchoolName" name="schoolName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editSchoolCode" class="form-label">Provider Code</label>
                                <input type="text" class="form-control" id="editSchoolCode" name="schoolCode" required>
                            </div>

                            <div class="col-md-6">
                                <label for="editSchoolAddress" class="form-label">Address</label>
                                <input type="text" class="form-control" id="editSchoolAddress" name="schoolAddress" required>
                            </div>

                            <div class="col-md-6">
                                <label for="editContactEmail" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="editContactEmail" name="contactEmail" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editContactPhone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="editContactPhone" name="contactPhone" required>
                            </div>

                            <div class="col-md-6">
                                <label for="editAccreditationCourse" class="form-label">Accreditation Course</label>
                                <select class="form-select" id="editAccreditationCourse" name="accreditation_course" required>
                                    <option value="" disabled>Select accreditation course</option>
                                    <option value="PPL">PPL</option>
                                    <option value="CPL">CPL</option>
                                    <option value="IR">IR</option>
                                    <option value="ME">ME</option>
                                    <option value="FI">FI</option>
                                    <option value="GI">GI</option>
                                </select>
                            </div>

                            <!-- Current Files Area -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Current Attachments</label>
                                <div id="edit_existing_attachments_list" class="d-flex flex-wrap gap-2 mb-2"></div>
                                <div id="deleted_attachments_container"></div>
                            </div>

                            <!-- New Uploads Area -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Add New Attachments</label>
                                <div class="file-upload-drag-area border border-dashed rounded-3 p-4 text-center position-relative mb-2" style="border-style: dashed !important; border-color: var(--purple) !important; background-color: var(--cobalt-light);">
                                    <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" id="edit_atoc_attachment" name="atoc_attachment[]" multiple style="cursor: pointer;">
                                    <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary"></i>
                                    <p class="mb-1 mt-2 fw-semibold">Drag & Drop files here or click to browse</p>
                                    <p class="text-muted small">Upload multiple files (PDF, images, documents)</p>
                                </div>
                                <div id="edit_selected_files_list" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Provider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Global Delete Form -->
    <form id="globalDeleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- View School Modal -->
    <div class="modal fade" id="viewSchoolModal" tabindex="-1" aria-labelledby="viewSchoolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSchoolModalLabel">Training Provider Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Provider Name</label>
                        <h5 id="viewSchoolName" class="text-primary fw-semibold"></h5>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Provider Code</label>
                            <div id="viewSchoolCode" class="fw-medium"></div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Accreditation Course</label>
                            <div id="viewAccreditationCourse" class="fw-medium"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Address</label>
                        <div id="viewSchoolAddress"></div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Email</label>
                            <div><a id="viewContactEmail" href=""></a></div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Phone</label>
                            <div id="viewContactPhone"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">ATOC Attachments</label>
                        <div id="viewAttachmentsList" class="list-group"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
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
                columnDefs: [{
                    targets: [5],
                    orderable: false,
                    searchable: false
                }],
            });
        }

        initTrainingDataTable();

        // ================= DYNAMIC ATTACHMENTS (ADD FORM) =================
        let addSelectedFiles = [];
        const addFileInput = document.getElementById('add_atoc_attachment');
        const addFileListContainer = document.getElementById('add_selected_files_list');

        if (addFileInput) {
            addFileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    if (!addSelectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                        addSelectedFiles.push(file);
                    }
                });
                renderAddSelectedFiles();
            });
        }

        function renderAddSelectedFiles() {
            if (!addFileListContainer) return;
            addFileListContainer.innerHTML = '';
            addSelectedFiles.forEach((file, index) => {
                const fileCard = document.createElement('div');
                fileCard.className = 'badge bg-light text-dark border p-2 d-flex align-items-center gap-2 rounded-pill shadow-sm';
                fileCard.style.whiteSpace = 'normal';
                fileCard.innerHTML = `
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="text-truncate" style="max-width: 150px;" title="${file.name}">${file.name}</span>
                    <span class="text-muted small">${(file.size / 1024).toFixed(1)} KB</span>
                    <button type="button" class="btn-close text-danger ms-1" style="font-size: 0.65rem;" onclick="removeAddFile(${index})"></button>
                `;
                addFileListContainer.appendChild(fileCard);
            });
            updateAddFileInput();
        }

        function updateAddFileInput() {
            if (!addFileInput) return;
            const dt = new DataTransfer();
            addSelectedFiles.forEach(file => dt.items.add(file));
            addFileInput.files = dt.files;
        }

        window.removeAddFile = function(index) {
            addSelectedFiles.splice(index, 1);
            renderAddSelectedFiles();
        };

        // ================= VIEW PROVIDER DETAILS =================
        $(document).on('click', '.btn-view-provider', function() {
            const btn = $(this);
            $('#viewSchoolName').text(btn.data('name'));
            $('#viewSchoolCode').text(btn.data('code'));
            $('#viewSchoolAddress').text(btn.data('address'));
            $('#viewContactEmail').text(btn.data('email')).attr('href', 'mailto:' + btn.data('email'));
            $('#viewContactPhone').text(btn.data('phone'));
            $('#viewAccreditationCourse').text(btn.data('course'));
            
            const attachmentsList = $('#viewAttachmentsList');
            attachmentsList.empty();
            
            let attachments = [];
            try {
                attachments = btn.data('attachments');
            } catch(e) {}
            
            if (attachments && attachments.length > 0) {
                attachments.forEach(file => {
                    const fileName = file.split('/').pop().substring(11); // remove timestamp prefix
                    attachmentsList.append(`
                        <a href="/${file}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-arrow-down text-primary me-2"></i>${fileName}</span>
                            <span class="badge bg-primary rounded-pill">Download</span>
                        </a>
                    `);
                });
            } else {
                attachmentsList.append('<div class="text-muted small py-2">No attachments uploaded.</div>');
            }
            $('#viewSchoolModal').modal('show');
        });

        // ================= DYNAMIC ATTACHMENTS (EDIT FORM) =================
        let editSelectedFiles = [];
        let deletedAttachments = [];
        const editFileInput = document.getElementById('edit_atoc_attachment');
        const editFileListContainer = document.getElementById('edit_selected_files_list');

        if (editFileInput) {
            editFileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    if (!editSelectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                        editSelectedFiles.push(file);
                    }
                });
                renderEditSelectedFiles();
            });
        }

        function renderEditSelectedFiles() {
            if (!editFileListContainer) return;
            editFileListContainer.innerHTML = '';
            editSelectedFiles.forEach((file, index) => {
                const fileCard = document.createElement('div');
                fileCard.className = 'badge bg-light text-dark border p-2 d-flex align-items-center gap-2 rounded-pill shadow-sm';
                fileCard.style.whiteSpace = 'normal';
                fileCard.innerHTML = `
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="text-truncate" style="max-width: 150px;" title="${file.name}">${file.name}</span>
                    <span class="text-muted small">${(file.size / 1024).toFixed(1)} KB</span>
                    <button type="button" class="btn-close text-danger ms-1" style="font-size: 0.65rem;" onclick="removeEditFile(${index})"></button>
                `;
                editFileListContainer.appendChild(fileCard);
            });
            updateEditFileInput();
        }

        function updateEditFileInput() {
            if (!editFileInput) return;
            const dt = new DataTransfer();
            editSelectedFiles.forEach(file => dt.items.add(file));
            editFileInput.files = dt.files;
        }

        window.removeEditFile = function(index) {
            editSelectedFiles.splice(index, 1);
            renderEditSelectedFiles();
        };

        // Open Edit Modal & Load Details
        $(document).on('click', '.btn-edit-provider', function() {
            const btn = $(this);
            const id = btn.data('id');
            
            // Set dynamic action endpoint
            document.getElementById('editSchoolForm').action = `/superadmin/flight-school/${id}/update`;
            
            $('#editSchoolId').val(id);
            $('#editSchoolName').val(btn.data('name'));
            $('#editSchoolCode').val(btn.data('code'));
            $('#editSchoolAddress').val(btn.data('address'));
            $('#editContactEmail').val(btn.data('email'));
            $('#editContactPhone').val(btn.data('phone'));
            $('#editAccreditationCourse').val(btn.data('course'));
            
            // Reset files arrays
            editSelectedFiles = [];
            deletedAttachments = [];
            $('#edit_selected_files_list').empty();
            $('#deleted_attachments_container').empty();
            if (editFileInput) editFileInput.value = '';
            
            // Show existing files
            const existingContainer = $('#edit_existing_attachments_list');
            existingContainer.empty();
            
            let attachments = [];
            try {
                attachments = btn.data('attachments');
            } catch(e) {}
            
            if (attachments && attachments.length > 0) {
                attachments.forEach(file => {
                    const fileName = file.split('/').pop().substring(11); // remove timestamp
                    existingContainer.append(`
                        <div class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2 rounded-pill shadow-sm" data-path="${file}">
                            <i class="bi bi-file-earmark-check-fill text-success"></i>
                            <span class="text-truncate" style="max-width: 150px;" title="${fileName}">${fileName}</span>
                            <button type="button" class="btn-close text-danger ms-1 btn-remove-existing" style="font-size: 0.65rem;"></button>
                        </div>
                    `);
                });
            } else {
                existingContainer.append('<div class="text-muted small w-100">No attachments uploaded.</div>');
            }
            
            $('#editSchoolModal').modal('show');
        });

        // Handle existing attachment removal
        $(document).on('click', '.btn-remove-existing', function() {
            const parent = $(this).closest('.badge');
            const path = parent.data('path');
            deletedAttachments.push(path);
            
            // Add hidden input to form
            $('#deleted_attachments_container').append(`
                <input type="hidden" name="deleted_attachments[]" value="${path}">
            `);
            
            // Remove the badge from UI
            parent.remove();
            
            if ($('#edit_existing_attachments_list').children().length === 0) {
                $('#edit_existing_attachments_list').append('<div class="text-muted small w-100">No attachments uploaded.</div>');
            }
        });

        // ================= DELETE PROVIDER =================
        $(document).on('click', '.btn-delete-provider', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this Training Provider? All associated files will be deleted permanently.')) {
                const form = document.getElementById('globalDeleteForm');
                form.action = `/superadmin/flight-school/${id}`;
                form.submit();
            }
        });
    </script>
</body>

</html>
