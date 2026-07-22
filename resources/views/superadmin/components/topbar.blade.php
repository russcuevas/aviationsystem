<header class="topbar">
    <button class="toggle-btn" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
    <div class="topbar-actions">
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode">
            <i class="bi bi-moon-stars" id="themeToggleIcon"></i>
        </button>
        <div class="user-menu" id="userMenu">
            <button class="user-menu-trigger" id="userMenuToggle" type="button" aria-haspopup="true"
                aria-expanded="false">
                <div class="user-avatar"
                    style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--purple) 0%,#7f91ca 100%);color:var(--cobalt-dark);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                    RV</div>
                <div class="user-menu-trigger-text">
                    <div class="user-menu-trigger-name">Russel Vincent</div>
                    <div class="user-menu-trigger-role">NAAP Authority</div>
                </div>
                <i class="bi bi-chevron-down user-menu-trigger-chevron"></i>
            </button>
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <button class="user-menu-item" type="button">
                    <i class="bi bi-person"></i>
                    Profile
                </button>
                <button class="user-menu-item logout" type="button">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </button>
            </div>
        </div>
    </div>
</header>
