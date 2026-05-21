<aside class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar" style="background-color: var(--sidebar-bg); min-height: calc(100vh - 56px);">
    <div class="position-sticky pt-3">
        <button type="button" id="sidebarCollapseBtn" class="sidebar-collapse-btn" aria-label="Réduire / Agrandir le menu">
            <i class="fas fa-chevron-left"></i>
        </button>
        <nav class="nav flex-column">
            
            <!-- Dashboard -->
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home"></i> <span class="nav-text">Dashboard</span>
            </a>

            @php
                $user = Auth::user();
                $canViewLearners = $user && ($user->isSuperAdmin() || $user->hasPermission('view_learners'));
                $canCreateLearner = $user && ($user->isSuperAdmin() || $user->hasPermission('create_learner'));
                $canViewCourses = $user && ($user->isSuperAdmin() || $user->hasAnyPermission(['voir_formations', 'view_courses']));
                $canCreateCourse = $user && ($user->isSuperAdmin() || $user->hasAnyPermission(['ajouter_formation', 'create_course']));
                $canViewPedagogical = $user && ($user->isSuperAdmin() || $user->hasAnyPermission([ 'view_pedagogical', 'view_attendance', 'view_evaluations', 'view_exams', 'view_grades' ]));
                $canViewAttendance = $user && ($user->isSuperAdmin() || $user->hasPermission('view_attendance'));
                $canViewEvaluations = $user && ($user->isSuperAdmin() || $user->hasPermission('view_evaluations'));
                $canViewExams = $user && ($user->isSuperAdmin() || $user->hasPermission('view_exams'));
                $canViewGrades = $user && ($user->isSuperAdmin() || $user->hasPermission('view_grades'));
                $canViewFinances = $user && ($user->isSuperAdmin() || $user->hasAnyPermission([ 'view_finances', 'view_payments', 'view_expenses', 'view_revenue' ]));
                $canViewPayments = $user && ($user->isSuperAdmin() || $user->hasPermission('view_payments'));
                $canViewExpenses = $user && ($user->isSuperAdmin() || $user->hasPermission('view_expenses'));
                $canViewRevenue = $user && ($user->isSuperAdmin() || $user->hasPermission('view_revenue'));
                $canViewCertificates = $user && ($user->isSuperAdmin() || $user->hasPermission('view_certificates'));
            @endphp

            <!-- Gestion des Apprenants -->
            @if($user && $canViewLearners)
                <div class="nav-item dropdown-menu-like">
                    <a class="nav-link {{ request()->routeIs('admin.apprenants.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#apprenants-menu">
                        <i class="fas fa-graduation-cap"></i> <span class="nav-text">Apprenants</span>
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 12px;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.apprenants.*') ? 'show' : '' }}" id="apprenants-menu">
                        <a class="nav-link" style="padding-left: 40px; font-size: 13px;" 
                           href="{{ route('admin.apprenants.index') }}">
                            <i class="fas fa-list"></i> <span class="nav-text">Liste apprenants</span>
                        </a>
                        @if($canCreateLearner)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" 
                               href="{{ route('admin.apprenants.create') }}">
                                <i class="fas fa-plus-circle"></i> <span class="nav-text">Ajouter apprenant</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Gestion des Formations -->
            @if($user && $canViewCourses)
                <div class="nav-item dropdown-menu-like">
                    <a class="nav-link {{ request()->routeIs('admin.formations.*') || request()->routeIs('admin.categories-formations.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#formations-menu">
                        <i class="fas fa-book"></i> <span class="nav-text">Gestion des formations</span>
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 12px;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.formations.*') || request()->routeIs('admin.categories-formations.*') ? 'show' : '' }}" id="formations-menu">
                        <a class="nav-link" style="padding-left: 40px; font-size: 13px;" 
                           href="{{ route('admin.formations.index') }}">
                            <i class="fas fa-list"></i> <span class="nav-text">Liste formations</span>
                        </a>
                        @if($canCreateCourse)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" 
                               href="{{ route('admin.formations.create') }}">
                                <i class="fas fa-plus-circle"></i> <span class="nav-text">Ajouter formation</span>
                            </a>
                        @endif
                        <a class="nav-link" style="padding-left: 40px; font-size: 13px;" 
                           href="{{ route('admin.categories-formations.index') }}">
                            <i class="fas fa-tags"></i> <span class="nav-text">Catégories formations</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Gestion Pédagogique -->
            @if($user && $canViewPedagogical)
                <div class="nav-item dropdown-menu-like">
                    <a class="nav-link {{ request()->routeIs('admin.pedagogie.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#pedagogique-menu">
                        <i class="fas fa-chalkboard"></i> <span class="nav-text">Pédagogique</span>
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 12px;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.pedagogie.*') ? 'show' : '' }}" id="pedagogique-menu">
                        @if($canViewAttendance)
                            <a class="nav-link {{ request()->routeIs('admin.pedagogie.presences') ? 'active' : '' }}" 
                               style="padding-left: 40px; font-size: 13px;" 
                               href="{{ route('admin.pedagogie.presences') }}">
                                <i class="fas fa-clipboard-check"></i> <span class="nav-text">Présences</span>
                            </a>
                        @endif
                        @if($canViewEvaluations)
                            <a class="nav-link {{ request()->routeIs('admin.pedagogie.evaluations') ? 'active' : '' }}" 
                               style="padding-left: 40px; font-size: 13px;" 
                               href="{{ route('admin.pedagogie.evaluations') }}">
                                <i class="fas fa-chart-line"></i> <span class="nav-text">Évaluations</span>
                            </a>
                        @endif
                        @if($canViewExams)
                            <a class="nav-link {{ request()->routeIs('admin.pedagogie.examens') ? 'active' : '' }}" 
                               style="padding-left: 40px; font-size: 13px;" 
                               href="{{ route('admin.pedagogie.examens') }}">
                                <i class="fas fa-pencil-alt"></i> <span class="nav-text">Examens</span>
                            </a>
                        @endif
                        @if($canViewGrades)
                            <a class="nav-link {{ request()->routeIs('admin.pedagogie.notes') ? 'active' : '' }}" 
                               style="padding-left: 40px; font-size: 13px;" 
                               href="{{ route('admin.pedagogie.notes') }}">
                                <i class="fas fa-star"></i> <span class="nav-text">Notes</span>
                            </a>
                        @endif
                        <a class="nav-link {{ request()->routeIs('admin.pedagogie.resultats') ? 'active' : '' }}" 
                           style="padding-left: 40px; font-size: 13px;" 
                           href="{{ route('admin.pedagogie.resultats') }}">
                            <i class="fas fa-poll"></i> <span class="nav-text">Résultats</span>
                        </a>
                    </div>
                </div>
            @endif


            <!-- Gestion Financière -->
            @if($user && $canViewFinances)
                <div class="nav-section-title mt-4">Finances</div>
                <div class="nav-item dropdown-menu-like">
                    <a class="nav-link {{ request()->routeIs('admin.finances.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#finances-menu">
                        <i class="fas fa-money-bill-wave"></i> <span class="nav-text">Gestion Financière</span>
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 12px;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.finances.*') ? 'show' : '' }}" id="finances-menu">
                        <a class="nav-link {{ request()->routeIs('admin.finances.index') ? 'active' : '' }}" 
                           style="padding-left: 40px; font-size: 13px;" href="{{ route('admin.finances.index') }}">
                            <i class="fas fa-chart-pie"></i> <span class="nav-text">Vue d'ensemble</span>
                        </a>
                        @if($canViewPayments)
                            <a class="nav-link {{ request()->routeIs('admin.finances.payments') ? 'active' : '' }}" 
                               style="padding-left: 40px; font-size: 13px;" href="{{ route('admin.finances.payments') }}">
                                <i class="fas fa-credit-card"></i> <span class="nav-text">Paiements</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.finances.trainer_payments') ? 'active' : '' }}" 
                               style="padding-left: 40px; font-size: 13px;" href="{{ route('admin.finances.trainer_payments') }}">
                                <i class="fas fa-hand-holding-usd"></i> <span class="nav-text">Rémunération Formateurs</span>
                            </a>
                        @endif
                        @if($canViewExpenses)
                            <a class="nav-link {{ request()->routeIs('admin.finances.expenses') ? 'active' : '' }}" 
                               style="padding-left: 40px; font-size: 13px;" href="{{ route('admin.finances.expenses') }}">
                                <i class="fas fa-shopping-cart"></i> <span class="nav-text">Dépenses</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Attestations -->
            @if($user && $canViewCertificates)
                <a class="nav-link {{ request()->routeIs('admin.attestations.*') ? 'active' : '' }}" 
                   href="{{ route('admin.attestations.index') }}">
                    <i class="fas fa-certificate"></i> <span class="nav-text">Attestations</span>
                </a>
            @endif

            <!-- Gestion Documentaire -->
            <!-- Documents et Rapports supprimés par l'utilisateur -->

            <!-- Traçabilité supprimée -->

            <!-- Paramètres (nouveau) -->
            <div class="nav-section-title mt-4">Système</div>
            <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" 
               href="{{ route('admin.settings') }}">
                <i class="fas fa-cog"></i> <span class="nav-text">Paramètres</span>
            </a>

        </nav>
    </div>
</aside>

<style>
    .nav-item.dropdown-menu-like .collapse {
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .nav-item.dropdown-menu-like .collapse.show {
        display: block;
    }
</style>
