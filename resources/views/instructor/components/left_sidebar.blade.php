<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo"><i class="bi bi-airplane-engines"></i></div>
        <div class="brand-text">
            <p>NAAP</p>
            <h6>Aviation TMS</h6>
        </div>
    </div>

    <div style="overflow-y:auto; flex:1; padding-bottom:10px;">
        <div class="nav-section-label">Instructor Menu</div>
        <nav class="nav flex-column px-0" style="gap:2px;">
            <a class="nav-link {{ request()->routeIs('instructor.dashboard.page') ? 'active' : '' }}"
                href="{{ route('instructor.dashboard.page') }}"><i
                    class="bi bi-grid-1x2-fill nav-icon"></i><span>Dashboard</span></a>
            <a class="nav-link {{ request()->routeIs('instructor.scheduling.page') ? 'active' : '' }}"
                href="{{ route('instructor.scheduling.page') }}"><i
                    class="bi bi-calendar2-check nav-icon"></i><span>Schedule
                    Viewing</span></a>
            <a class="nav-link {{ request()->routeIs('instructor.aircraft.logbooks.page') ? 'active' : '' }}"
                href="{{ route('instructor.aircraft.logbooks.page') }}"><i
                    class="bi bi-journal-plus nav-icon"></i><span>Aircraft
                    Logbook Entry</span></a>
            <a class="nav-link" href="#"><i class="bi bi-graph-up-arrow nav-icon"></i><span>Student
                    Progress Update</span></a>
            <a class="nav-link {{ request()->routeIs('instructor.flight.hours.encoding.page') ? 'active' : '' }}"
                href="{{ route('instructor.flight.hours.encoding.page') }}"><i class="bi bi-stopwatch nav-icon"></i><span>Flight Hours
                    Encoding</span></a>
            <a class="nav-link" href="#"><i class="bi bi-journal-text nav-icon"></i><span>Grade
                    Sheet Submission</span></a>
        </nav>
    </div>
</aside>
