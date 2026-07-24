<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo"><i class="bi bi-airplane-engines"></i></div>
        <div class="brand-text">
            <p>NAAP</p>
            <h6>Aviation TMS</h6>
        </div>
    </div>

    <div style="overflow-y:auto; flex:1; padding-bottom:10px;">
        <div class="nav-section-label">Main Menu</div>
        <nav class="nav flex-column px-0" style="gap:2px;">
            <a class="nav-link active" href="{{ route('superadmin.dashboard.page') }}">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>
                <span>Overview</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.flight.school.page') }}">
                <i class="bi bi-airplane-engines nav-icon"></i>
                <span>Flying Schools</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.student.page') }}">
                <i class="bi bi-people nav-icon"></i>
                <span>Students</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.instructor.page') }}">
                <i class="bi bi-person-badge nav-icon"></i>
                <span>Instructors</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.aircraft.page') }}">
                <i class="bi bi-airplane nav-icon"></i>
                <span>Aircraft</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.flight.hours.page') }}">
                <i class="bi bi-stopwatch nav-icon"></i>
                <span>Flight Hours</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.grade.sheets.page') }}">
                <i class="bi bi-journal-text nav-icon"></i>
                <span>Grade Sheets</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.aircraft.logbook.page') }}">
                <i class="bi bi-journal-bookmark nav-icon"></i>
                <span>Logbooks</span>
            </a>
            <a class="nav-link" href="{{ route('superadmin.reports.page') }}">
                <i class="bi bi-file-earmark-bar-graph nav-icon"></i>
                <span>Reports</span>
            </a>
        </nav>
    </div>
</aside>
