<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo"><i class="bi bi-airplane-engines"></i></div>
        <div class="brand-text">
            <p>NAAP</p>
            <h6>Aviation TMS</h6>
        </div>
    </div>

    <div style="overflow-y:auto; flex:1; padding-bottom:10px;">
        <div class="nav-section-label">Admin Menu</div>
        <nav class="nav flex-column px-0" style="gap:2px;">
            <a class="nav-link active" href="{{ route('admin.dashboard.page') }}"><i
                    class="bi bi-grid-1x2-fill nav-icon"></i><span>Overview</span></a>
            <a class="nav-link" href="{{ route('admin.scheduling.page') }}"><i
                    class="bi bi-calendar2-check nav-icon"></i><span>Scheduling</span></a>
            <a class="nav-link" href="logbooks.html"><i class="bi bi-journal-bookmark nav-icon"></i><span>Aircraft
                    Logbooks</span></a>
            <a class="nav-link" href="student_progress.html"><i class="bi bi-graph-up-arrow nav-icon"></i><span>Student
                    Progress</span></a>
            <a class="nav-link" href="flight_hours_validation.html"><i class="bi bi-stopwatch nav-icon"></i><span>Flight
                    Hours Validation</span></a>
            <a class="nav-link" href="grade_sheets.html"><i class="bi bi-journal-text nav-icon"></i><span>Grade
                    Sheets</span></a>
            <a class="nav-link" href="reports.html"><i
                    class="bi bi-file-earmark-bar-graph nav-icon"></i><span>Reports</span></a>
        </nav>
    </div>
</aside>
