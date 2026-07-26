<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo"><i class="bi bi-airplane-engines"></i></div>
        <div class="brand-text">
            <p>NAAP</p>
            <h6>Aviation TMS</h6>
        </div>
    </div>

    <div style="overflow-y:auto; flex:1; padding-bottom:10px;">
        <div class="nav-section-label">Student Menu</div>
        <nav class="nav flex-column px-0" style="gap:2px;">
            <a class="nav-link {{ request()->routeIs('student.dashboard.page') ? 'active' : '' }}"
                href="{{ route('student.dashboard.page') }}"><i
                    class="bi bi-grid-1x2-fill nav-icon"></i><span>Dashboard</span></a>
            <a class="nav-link {{ request()->routeIs('student.scheduling.page') ? 'active' : '' }}"
                href="{{ route('student.scheduling.page') }}"><i
                    class="bi bi-calendar2-check nav-icon"></i><span>Schedule
                    Viewing</span></a>
            <a class="nav-link {{ request()->routeIs('student.flight.hours.page') ? 'active' : '' }}"
                href="{{ route('student.flight.hours.page') }}"><i class="bi bi-stopwatch nav-icon"></i><span>Flight
                    Hours
                    Tracking</span></a>
            <a class="nav-link {{ request()->routeIs('student.training.progress.page') ? 'active' : '' }}"
                href="{{ route('student.training.progress.page') }}"><i
                    class="bi bi-graph-up-arrow nav-icon"></i><span>Training Progress</span></a>
            <a class="nav-link {{ request()->routeIs('student.grades.page') ? 'active' : '' }}"
                href="{{ route('student.grades.page') }}"><i class="bi bi-journal-check nav-icon"></i><span>Grade
                    Viewing</span></a>
        </nav>
    </div>
</aside>
