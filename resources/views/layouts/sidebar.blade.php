<aside class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar" style="background-color: var(--sidebar-bg); min-height: calc(100vh - 56px);">
    <div class="position-sticky pt-3">
        <button type="button" id="sidebarCollapseBtn" class="sidebar-collapse-btn" aria-label="Réduire / Agrandir le menu">
            <i class="fas fa-chevron-left"></i>
        </button>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home"></i> <span class="nav-text">Tableau de bord</span>
            </a>

            <a class="nav-link {{ request()->routeIs('admin.cards.*') && !request()->routeIs('admin.cards.grade-updates*') ? 'active' : '' }}"
               href="{{ route('admin.cards.index') }}">
                <i class="fas fa-id-card"></i> <span class="nav-text">Gestion des cartes</span>
            </a>

            <a class="nav-link {{ request()->routeIs('admin.cards.grade-updates*') ? 'active' : '' }}"
               href="{{ route('admin.cards.grade-updates') }}">
                <i class="fas fa-layer-group"></i> <span class="nav-text">Passage de grade</span>
            </a>

            <a class="nav-link {{ request()->routeIs('admin.settings') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.settings') }}">
                <i class="fas fa-cog"></i> <span class="nav-text">Paramètres</span>
            </a>
        </nav>
    </div>
</aside>
