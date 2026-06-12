<aside class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar" style="background-color: var(--sidebar-bg); min-height: calc(100vh - 56px);">
    <div class="position-sticky pt-3">
        <button type="button" id="sidebarCollapseBtn" class="sidebar-collapse-btn" aria-label="{{ __('messages.menu_toggle') }}">
            <i class="fas fa-chevron-left"></i>
        </button>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home"></i> <span class="nav-text">{{ __('messages.dashboard') }}</span>
            </a>

            <a class="nav-link {{ request()->routeIs('admin.cards.*') && !request()->routeIs('admin.cards.grade-updates*') ? 'active' : '' }}"
               href="{{ route('admin.cards.index') }}">
                <i class="fas fa-id-card"></i> <span class="nav-text">{{ __('messages.cards_management') }}</span>
            </a>

            <a class="nav-link {{ request()->routeIs('admin.cards.grade-updates*') ? 'active' : '' }}"
               href="{{ route('admin.cards.grade-updates') }}">
                <i class="fas fa-layer-group"></i> <span class="nav-text">{{ __('messages.grade_updates') }}</span>
            </a>

            @if(Auth::user()?->isSuperAdmin() || Auth::user()?->hasAnyPermission(['view_school_cards', 'create_school_card', 'edit_school_card', 'delete_school_card', 'manage_school_card_settings']))
                <a class="nav-link {{ request()->routeIs('admin.school-cards.*') ? 'active' : '' }}"
                   href="{{ route('admin.school-cards.index') }}">
                    <i class="fas fa-school"></i> <span class="nav-text">{{ __('messages.school_cards_management') }}</span>
                </a>
            @endif

            <a class="nav-link {{ request()->routeIs('admin.settings') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.settings') }}">
                <i class="fas fa-cog"></i> <span class="nav-text">{{ __('messages.settings') }}</span>
            </a>
        </nav>
    </div>
</aside>
