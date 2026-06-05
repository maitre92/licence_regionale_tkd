@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
<div class="row h-100">
    <!-- Colonne Gauche - Menu -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header settings-sidebar-header text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bars"></i> Menu
                </h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="users-list">
                    <i class="fas fa-list"></i> Liste Utilisateurs
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="permissions-list">
                    <i class="fas fa-th-list"></i> Liste Permissions
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="permissions-assign">
                    <i class="fas fa-user-check"></i> Assigner Permissions
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="salles-list">
                    <i class="fas fa-door-open"></i> Salles
                </a>
            </div>
        </div>
    </div>

    <!-- Colonne Droite - Contenu -->
    <div class="col-md-9">

        <!-- Liste Utilisateurs -->
        <div class="settings-content" id="users-list" style="display: none;">
            <div class="card">
                <div class="card-header settings-card-header d-flex align-items-center justify-content-between text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Liste Utilisateurs
                    </h5>
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('create_user'))
                        <a href="#" class="btn btn-sm btn-sombre" id="btn-create-user" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="fas fa-plus"></i> Utilisateur
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users ?? [] as $u)
                                    @php
                                        $currentRole = \App\Shared\Enums\UserRole::tryFrom(Auth::user()->role);
                                        $targetRole = \App\Shared\Enums\UserRole::tryFrom($u->role);
                                        $canToggleAccount = (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_user'))
                                            && Auth::id() !== $u->id
                                            && $targetRole
                                            && !in_array($targetRole, [\App\Shared\Enums\UserRole::SUPERADMIN, \App\Shared\Enums\UserRole::ADMIN], true)
                                            && ($currentRole === \App\Shared\Enums\UserRole::SUPERADMIN || ($currentRole && $currentRole->canManage($targetRole)));
                                        $isAccountActive = (bool) $u->is_active && (string) $u->status === \App\Shared\Enums\UserStatus::ACTIVE->value;
                                    @endphp
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->role }}</td>
                                        <td>{{ $u->status }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_user'))
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary btn-edit-user"
                                                            title="Modifier"
                                                            data-id="{{ $u->id }}"
                                                            data-name="{{ $u->name }}"
                                                            data-email="{{ $u->email }}"
                                                            data-phone="{{ $u->phone }}"
                                                            data-specialite="{{ $u->specialite }}"
                                                            data-diplome="{{ $u->diplome }}"
                                                            data-adresse="{{ $u->adresse }}"
                                                            data-role="{{ $u->role }}"
                                                            data-status="{{ $u->status }}"
                                                            data-active="{{ $u->is_active ? '1' : '0' }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endif
                                                @if($canToggleAccount)
                                                    <form method="POST"
                                                          action="{{ $isAccountActive ? route('admin.users.deactivate', $u) : route('admin.users.activate', $u) }}"
                                                          class="d-inline toggle-user-form"
                                                          data-message="{{ $isAccountActive ? 'Désactiver ce compte utilisateur ?' : 'Activer ce compte utilisateur ?' }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="btn btn-sm {{ $isAccountActive ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                                title="{{ $isAccountActive ? 'Désactiver' : 'Activer' }}">
                                                            <i class="fas {{ $isAccountActive ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_user'))
                                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline delete-user-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="back_to_settings" value="1">
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-user" data-user-name="{{ $u->name }}" title="Supprimer">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste Permissions -->
        <div class="settings-content" id="permissions-list" style="display: none;">
            <div class="card">
                <div class="card-header settings-card-header d-flex align-items-center justify-content-between text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-th-list"></i> Liste Permissions
                    </h5>
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('create_permission') || Auth::user()->hasPermission('manage_permissions'))
                        <a href="#" class="btn btn-sm btn-sombre" id="btn-create-permission" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                            <i class="fas fa-plus"></i> Permission
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-8">
                            <label for="permission_search" class="form-label">Recherche</label>
                            <input type="text"
                                   id="permission_search"
                                   class="form-control"
                                   value="{{ $permissionSearch ?? '' }}"
                                   placeholder="Rechercher par nom, module ou identifiant"
                                   autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-secondary" id="btn-clear-permission-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Module</th>
                                    <th>Identifiant</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions ?? [] as $p)
                                    <tr class="permission-row" data-search="{{ strtolower($p->name . ' ' . $p->module . ' ' . $p->slug) }}">
                                        <td>{{ $p->name }}</td>
                                        <td>{{ $p->module }}</td>
                                        <td>{{ $p->slug }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('manage_permissions'))
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary btn-edit-permission"
                                                            title="Modifier"
                                                            data-id="{{ $p->id }}"
                                                            data-name="{{ $p->name }}"
                                                            data-module="{{ $p->module }}"
                                                            data-slug="{{ $p->slug }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endif
                                                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_permission') || Auth::user()->hasPermission('manage_permissions'))
                                                    <form method="POST" action="{{ route('admin.permissions.destroy', $p) }}" class="d-inline delete-permission-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-permission" data-permission-name="{{ $p->name }}" title="Supprimer">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Aucune permission trouvée</td>
                                    </tr>
                                @endforelse
                                <tr id="permissions-empty-search" class="d-none">
                                    <td colspan="4" class="text-center py-4 text-muted">Aucune permission ne correspond à la recherche</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigner Permissions -->
        <div class="settings-content" id="permissions-assign" style="display: none;">
            <div class="card">
                <div class="card-header settings-card-header text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check"></i> + Assigner Permissions à un Utilisateur
                    </h5>
                </div>
                <div class="card-body">
                    <form id="assignPermissionsForm" action="{{ route('admin.permissions.assign') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="user_select" class="form-label">Sélectionner un Utilisateur</label>
                            @php $currentUser = Auth::user(); @endphp
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_select" name="user_id" required>
                                <option value="">-- Choisir un utilisateur --</option>
                                @foreach($users ?? [] as $user)
                                    @if(!$currentUser->isSuperAdmin() && $user->isSuperAdmin())
                                        @continue
                                    @endif
                                    <option value="{{ $user->id }}"
                                            data-superadmin="{{ $user->isSuperAdmin() ? '1' : '0' }}"
                                            data-permissions="{{ $user->permissions->pluck('id')->join(',') }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn_select_all">
                                    <i class="fas fa-check-square"></i> Tout sélectionner
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn_deselect_all">
                                    <i class="fas fa-square"></i> Tout désélectionner
                                </button>
                            </div>
                            <div class="text-muted small">Sélectionnez ou désélectionnez toutes les permissions visibles.</div>
                        </div>

                        <!-- Tableau des Permissions par Module -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th colspan="4" class="text-center">Permissions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permissionsByModule ?? [] as $module => $modulePermissions)
                                        <tr>
                                            <td colspan="5" class="table-active fw-bold 1875rem">
                                                <i class="fas fa-folder"></i> {{ $module }}
                                            </td>
                                        </tr>
                                        @foreach($modulePermissions as $permission)
                                            <tr>
                                                <td style="padding-left: 40px;">{{ $permission->name }}</td>
                                                <td class="text-center">
                                                    <div class="form-check">
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-check-input permission-checkbox" 
                                                            id="perm_{{ $permission->id }}"
                                                            name="permissions[]"
                                                            value="{{ $permission->id }}"
                                                            data-permission="{{ $permission->slug }}"
                                                        >
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Aucune permission disponible
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-secondary" id="btn_cancel">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary" id="btn_save_permissions">
                                <i class="fas fa-save"></i> Sauvegarder Permissions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Salles -->
        <div class="settings-content" id="salles-list" style="display: none;">
            <div class="card mb-4">
                <div class="card-header settings-card-header text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-door-open"></i> Salles
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.salles.store') }}" method="POST" class="row g-3 align-items-end mb-4">
                        @csrf
                        <div class="col-md-4">
                            <label for="salle_nom" class="form-label">Nom de la salle</label>
                            <input type="text" name="nom" id="salle_nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required placeholder="ex: Salle A1">
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label for="salle_capacite" class="form-label">Capacité</label>
                            <input type="number" min="0" name="capacite" id="salle_capacite" class="form-control @error('capacite') is-invalid @enderror" value="{{ old('capacite') }}">
                            @error('capacite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="salle_description" class="form-label">Description</label>
                            <input type="text" name="description" id="salle_description" class="form-control" value="{{ old('description') }}" placeholder="Bâtiment, étage, équipement...">
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" name="is_active" value="0">
                            <div class="form-check form-switch d-block mb-2">
                                <input class="form-check-input" type="checkbox" id="salle_is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="salle_is_active">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Capacité</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salles ?? [] as $salle)
                                    <tr>
                                        <td class="fw-bold">{{ $salle->nom }}</td>
                                        <td>{{ $salle->capacite ? $salle->capacite . ' places' : '-' }}</td>
                                        <td>{{ $salle->description ?: '-' }}</td>
                                        <td>
                                            @if($salle->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editSalleModal{{ $salle->id }}"
                                                        title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="{{ route('admin.settings.salles.destroy', $salle) }}" class="d-inline delete-salle-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-salle" data-salle-name="{{ $salle->nom }}" title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editSalleModal{{ $salle->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title"><i class="fas fa-edit"></i> Modifier la salle</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <form action="{{ route('admin.settings.salles.update', $salle) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nom de la salle</label>
                                                            <input type="text" name="nom" class="form-control" value="{{ old('nom', $salle->nom) }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Capacité</label>
                                                            <input type="number" min="0" name="capacite" class="form-control" value="{{ old('capacite', $salle->capacite) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea name="description" class="form-control" rows="3">{{ old('description', $salle->description) }}</textarea>
                                                        </div>
                                                        <input type="hidden" name="is_active" value="0">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_active" id="salle_active_{{ $salle->id }}" value="1" {{ $salle->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="salle_active_{{ $salle->id }}">Salle active</label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Aucune salle enregistrée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Changer mot de passe -->
        <div class="settings-content" id="change-password" style="display: none;">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-key"></i> + Changer mot de passe
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .settings-menu-item {
        border-left: 3px solid transparent;
        transition: all 0.3s;
    }

    .bg-dark-clair {
        background-color: #2b3038 !important;
    }

    .table.table-hover {
        border: 1px solid rgba(64, 96, 160, 0.35);
        border-radius: 12px;
        overflow: hidden;
    }

    .table.table-hover thead th {
        background-color: transparent;
        color: var(--body-text);
        border-bottom: 1px solid rgba(64, 96, 160, 0.18);
        padding: 14px 16px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .table.table-hover tbody tr {
        border-bottom: 1px solid rgba(64, 96, 160, 0.12);
        background-color: var(--card-bg);
    }

    .table.table-hover tbody tr:hover {
        background-color: rgba(64, 96, 160, 0.08);
    }

    .table.table-hover tbody td {
        border: none;
        padding: 14px 16px;
        vertical-align: middle;
    }

    .table.table-hover .table-active {
        background-color: #4060a0;
        color: #ffffff;
        border-bottom: 1px solid rgba(64, 96, 160, 0.35);
    }

    /* Use navbar/sidebar theme colors for module headers in "Assigner Permissions" */
    #permissions-assign .table-active {
        background-color: var(--navbar-bg) !important;
        color: var(--navbar-text) !important;
        border-bottom: 1px solid rgba(255,255,255,0.08) !important;
    }

    /* Ensure dark mode uses same variables (var values adjust in dark-mode) */
    html.dark-mode #permissions-assign .table-active {
        background-color: var(--navbar-bg) !important;
        color: var(--navbar-text) !important;
        border-bottom: 1px solid rgba(255,255,255,0.06) !important;
    }

    .form-check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        margin-bottom: 0;
    }

    .form-check-input.permission-checkbox {
        width: 20px;
        height: 20px;
        accent-color: var(--primary-color);
        cursor: pointer;
        box-shadow: inset 0 0 0 1px rgba(64, 96, 160, 0.3);
    }

    .form-check-input.permission-checkbox:focus {
        box-shadow: 0 0 0 0.2rem rgba(64, 96, 160, 0.25);
    }

    .settings-menu-item:hover {
        background-color: #f8f9fa;
        border-left-color: var(--primary-color, #667eea);
    }

    .settings-menu-item.active {
        background-color: rgba(64, 96, 160, 0.12);
        border-left-color: var(--primary-color, #667eea);
        color: var(--primary-color, #667eea);
    }

    .settings-sidebar-header,
    .settings-card-header {
        background-color: var(--navbar-bg);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .settings-card-header h5 {
        color: #ffffff;
    }

    .btn-sombre {
        background-color: var(--navbar-bg);
        color: #ffffff;
        border-color: var(--navbar-bg);
    }

    .btn-sombre:hover,
    .btn-sombre:focus {
        background-color: rgba(21, 38, 69, 0.92);
        border-color: rgba(21, 38, 69, 0.92);
        color: #ffffff;
    }

    html.dark-mode .settings-menu-item:hover,
    html.dark-mode .settings-menu-item.active {
        background-color: #333;
    }
</style>

@endsection

@section('js')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modals -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="createUserModalLabel"><i class="fas fa-user-plus"></i> Nouvel utilisateur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="settingsUserForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="settings_user_method" value="POST">
                <input type="hidden" name="user_id" id="settings_user_id">
                <input type="hidden" name="back_to_settings" value="1">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_user_name" class="form-label">Prénom & Nom</label>
                        <input type="text" name="name" id="new_user_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_user_email" class="form-label">Email</label>
                        <input type="email" name="email" id="new_user_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_user_phone" class="form-label">Téléphone</label>
                        <input type="text" name="phone" id="new_user_phone" class="form-control">
                    </div>
                    <div id="settings_formateur_fields" class="border rounded p-3 mb-3 d-none">
                        <h6 class="mb-3">Informations formateur</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="new_user_specialite" class="form-label">Spécialité</label>
                                <input type="text" name="specialite" id="new_user_specialite" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="new_user_diplome" class="form-label">Diplôme</label>
                                <select name="diplome" id="new_user_diplome" class="form-select">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach(['DUT', 'BTS', 'LICENCE', 'MASTER', 'DEA', 'DOCTORAT', 'AUTRE'] as $diplome)
                                        <option value="{{ $diplome }}">{{ $diplome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="new_user_adresse" class="form-label">Adresse</label>
                                <input type="text" name="adresse" id="new_user_adresse" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_user_password" class="form-label" id="settings_user_password_label">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" name="password" id="new_user_password" class="form-control" required>
                            <button class="btn btn-outline-secondary settings-password-toggle" type="button" data-target="new_user_password" aria-label="Afficher le mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-none" id="settings_user_password_help">Laissez vide pour conserver le mot de passe actuel.</small>
                    </div>
                    <div class="mb-3">
                        <label for="new_user_password_confirmation" class="form-label" id="settings_user_password_confirmation_label">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="new_user_password_confirmation" class="form-control" required>
                            <button class="btn btn-outline-secondary settings-password-toggle" type="button" data-target="new_user_password_confirmation" aria-label="Afficher la confirmation du mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="new_user_role" class="form-label">Rôle</label>
                            <select name="role" id="new_user_role" class="form-select" required>
                                @foreach($roles ?? [] as $roleValue => $roleLabel)
                                    @if(Auth::user()->isSuperAdmin() || $roleValue !== 'superadmin')
                                        <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="new_user_status" class="form-label">Statut</label>
                            <select name="status" id="new_user_status" class="form-select" required>
                                @foreach($statuses ?? [] as $statusValue => $statusLabel)
                                    <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="new_user_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="new_user_active">Activer l'utilisateur</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="settings_user_submit">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="createPermissionModalLabel"><i class="fas fa-key"></i> Nouvelle permission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="permissionForm" action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="permission_method" value="PUT" disabled>
                <input type="hidden" name="permission_form_mode" id="permission_form_mode" value="create">
                <input type="hidden" name="permission_id" id="permission_id">
                <div class="modal-body">
                    @if($errors->any() && old('permission_form_mode'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="permission_name" class="form-label">Nom</label>
                        <input type="text" name="name" id="permission_name" class="form-control" value="{{ old('permission_form_mode') ? old('name') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="permission_module" class="form-label">Module</label>
                        <input type="text" name="module" id="permission_module" class="form-control" value="{{ old('permission_form_mode') ? old('module') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="permission_slug" class="form-label">Identifiant interne</label>
                        <input type="text"
                               name="slug"
                               id="permission_slug"
                               class="form-control bg-light"
                               value="{{ old('permission_form_mode') ? old('slug') : '' }}"
                               placeholder="ex: view_users"
                               readonly>
                        <small class="text-muted">Généré automatiquement à partir du nom.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="permission_submit">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.settings-menu-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Retirer active de tous les items
            document.querySelectorAll('.settings-menu-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Masquer tous les contenus
            document.querySelectorAll('.settings-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Afficher le contenu sélectionné
            const target = this.getAttribute('data-target');
            document.getElementById(target).style.display = 'block';
            if (target) {
                const url = new URL(window.location);
                url.searchParams.set('tab', target);
                history.replaceState(null, '', url);
            }
        });
    });

    // Activer un onglet via query param `tab`
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab') || 'users-list';
        const el = document.querySelector(`.settings-menu-item[data-target="${tab}"]`);
        if (el) {
            el.click();
        } else {
            const fallback = document.querySelector('.settings-menu-item[data-target="users-list"]');
            if (fallback) fallback.click();
        }

        if (typeof updatePermissionCheckboxes === 'function') {
            updatePermissionCheckboxes();
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: @json(session('warning')),
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        @endif
    });

    const userSelect = document.getElementById('user_select');
    const selectAllButton = document.getElementById('btn_select_all');
    const deselectAllButton = document.getElementById('btn_deselect_all');
    const permissionCheckboxes = () => Array.from(document.querySelectorAll('.permission-checkbox'));

    const updatePermissionCheckboxes = () => {
        if (!userSelect) {
            return;
        }

        const selectedOption = userSelect.selectedOptions.length > 0 ? userSelect.selectedOptions[0] : null;
        const isSuperAdminUser = selectedOption && selectedOption.dataset.superadmin === '1';
        const assignedPermissions = selectedOption && selectedOption.dataset.permissions
            ? selectedOption.dataset.permissions.split(',').filter(Boolean).map(id => Number(id))
            : [];

        permissionCheckboxes().forEach(cb => {
            if (isSuperAdminUser) {
                cb.checked = true;
                cb.disabled = true;
            } else {
                cb.checked = assignedPermissions.includes(Number(cb.value));
                cb.disabled = false;
            }
        });
    };

    const setAllPermissions = (checked) => {
        permissionCheckboxes().forEach(cb => {
            if (!cb.disabled) {
                cb.checked = checked;
            }
        });
    };

    if (userSelect) {
        userSelect.addEventListener('change', updatePermissionCheckboxes);
    }

    if (selectAllButton) {
        selectAllButton.addEventListener('click', function() {
            setAllPermissions(true);
        });
    }

    if (deselectAllButton) {
        deselectAllButton.addEventListener('click', function() {
            setAllPermissions(false);
        });
    }

    function confirmDeletion(button, title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(result => {
            if (result.isConfirmed) {
                button.closest('form')?.submit();
            }
        });
    }

    document.querySelectorAll('.btn-delete-user').forEach(button => {
        button.addEventListener('click', function() {
            confirmDeletion(this, 'Supprimer l\'utilisateur', `Voulez-vous vraiment supprimer ${this.dataset.userName} ?`);
        });
    });

    document.querySelectorAll('.toggle-user-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirmation',
                text: this.dataset.message || 'Confirmer cette action ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(result => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    const settingsUserModalElement = document.getElementById('createUserModal');
    const settingsUserModal = settingsUserModalElement ? new bootstrap.Modal(settingsUserModalElement) : null;
    const settingsUserForm = document.getElementById('settingsUserForm');
    const settingsUserTitle = document.getElementById('createUserModalLabel');
    const settingsUserSubmit = document.getElementById('settings_user_submit');
    const settingsUserMethod = document.getElementById('settings_user_method');
    const settingsUserId = document.getElementById('settings_user_id');
    const settingsPassword = document.getElementById('new_user_password');
    const settingsPasswordConfirmation = document.getElementById('new_user_password_confirmation');
    const settingsPasswordLabel = document.getElementById('settings_user_password_label');
    const settingsPasswordConfirmationLabel = document.getElementById('settings_user_password_confirmation_label');
    const settingsPasswordHelp = document.getElementById('settings_user_password_help');
    const settingsUserRole = document.getElementById('new_user_role');
    const settingsFormateurFields = document.getElementById('settings_formateur_fields');
    const settingsFormateurInputs = [
        document.getElementById('new_user_specialite'),
        document.getElementById('new_user_diplome'),
        document.getElementById('new_user_adresse')
    ].filter(Boolean);
    const roleFormateurValue = "{{ \App\Shared\Enums\UserRole::FORMATEUR->value }}";
    const settingsUserStoreUrl = "{{ route('admin.users.store') }}";
    const settingsUserUpdateBase = "{{ url('admin/users') }}";

    document.querySelectorAll('.settings-password-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');
            const shouldShow = input.type === 'password';

            input.type = shouldShow ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !shouldShow);
            icon.classList.toggle('fa-eye-slash', shouldShow);
            this.setAttribute('aria-label', shouldShow ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });
    });

    function toggleSettingsFormateurFields() {
        const isFormateur = settingsUserRole?.value === roleFormateurValue;
        settingsFormateurFields?.classList.toggle('d-none', !isFormateur);
        settingsFormateurInputs.forEach(input => input.required = isFormateur);
    }

    function prepareCreateUserModal() {
        if (!settingsUserForm) {
            return;
        }

        settingsUserForm.reset();
        settingsUserForm.action = settingsUserStoreUrl;
        settingsUserMethod.value = 'POST';
        settingsUserId.value = '';
        settingsUserTitle.innerHTML = '<i class="fas fa-user-plus"></i> Nouvel utilisateur';
        settingsUserSubmit.textContent = 'Créer';
        settingsPassword.required = true;
        settingsPasswordConfirmation.required = true;
        settingsPasswordLabel.textContent = 'Mot de passe';
        settingsPasswordConfirmationLabel.textContent = 'Confirmer le mot de passe';
        settingsPasswordHelp.classList.add('d-none');
        document.getElementById('new_user_active').checked = true;
        toggleSettingsFormateurFields();
    }

    document.getElementById('btn-create-user')?.addEventListener('click', prepareCreateUserModal);
    settingsUserRole?.addEventListener('change', toggleSettingsFormateurFields);

    document.querySelectorAll('.btn-edit-user').forEach(button => {
        button.addEventListener('click', function() {
            if (!settingsUserForm || !settingsUserModal) {
                return;
            }

            settingsUserForm.reset();
            settingsUserForm.action = `${settingsUserUpdateBase}/${this.dataset.id}`;
            settingsUserMethod.value = 'PUT';
            settingsUserId.value = this.dataset.id || '';
            settingsUserTitle.innerHTML = '<i class="fas fa-user-edit"></i> Modifier l\'utilisateur';
            settingsUserSubmit.textContent = 'Enregistrer';

            document.getElementById('new_user_name').value = this.dataset.name || '';
            document.getElementById('new_user_email').value = this.dataset.email || '';
            document.getElementById('new_user_phone').value = this.dataset.phone || '';
            document.getElementById('new_user_role').value = this.dataset.role || '';
            document.getElementById('new_user_specialite').value = this.dataset.specialite || '';
            document.getElementById('new_user_diplome').value = this.dataset.diplome || '';
            document.getElementById('new_user_adresse').value = this.dataset.adresse || '';
            document.getElementById('new_user_status').value = this.dataset.status || 'active';
            document.getElementById('new_user_active').checked = this.dataset.active === '1';

            settingsPassword.required = false;
            settingsPasswordConfirmation.required = false;
            settingsPasswordLabel.textContent = 'Mot de passe (optionnel)';
            settingsPasswordConfirmationLabel.textContent = 'Confirmer le mot de passe';
            settingsPasswordHelp.classList.remove('d-none');
            toggleSettingsFormateurFields();

            settingsUserModal.show();
        });
    });

    document.querySelectorAll('.btn-delete-permission').forEach(button => {
        button.addEventListener('click', function() {
            confirmDeletion(this, 'Supprimer la permission', `Voulez-vous vraiment supprimer ${this.dataset.permissionName} ?`);
        });
    });

    document.querySelectorAll('.btn-delete-salle').forEach(button => {
        button.addEventListener('click', function() {
            confirmDeletion(this, 'Supprimer la salle', `Voulez-vous vraiment supprimer ${this.dataset.salleName} ?`);
        });
    });

    const permissionModalElement = document.getElementById('createPermissionModal');
    const permissionModal = permissionModalElement ? new bootstrap.Modal(permissionModalElement) : null;
    const permissionForm = document.getElementById('permissionForm');
    const permissionTitle = document.getElementById('createPermissionModalLabel');
    const permissionMethod = document.getElementById('permission_method');
    const permissionFormMode = document.getElementById('permission_form_mode');
    const permissionId = document.getElementById('permission_id');
    const permissionName = document.getElementById('permission_name');
    const permissionSlug = document.getElementById('permission_slug');
    const permissionSubmit = document.getElementById('permission_submit');
    const permissionSearch = document.getElementById('permission_search');
    const permissionRows = Array.from(document.querySelectorAll('.permission-row'));
    const emptyPermissionSearch = document.getElementById('permissions-empty-search');
    const permissionStoreUrl = "{{ route('admin.permissions.store') }}";
    const permissionUpdateBase = "{{ url('admin/permissions') }}";

    function filterPermissions() {
        const term = (permissionSearch?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        permissionRows.forEach(row => {
            const isVisible = !term || (row.dataset.search || '').includes(term);
            row.classList.toggle('d-none', !isVisible);
            if (isVisible) {
                visibleCount++;
            }
        });

        emptyPermissionSearch?.classList.toggle('d-none', !term || permissionRows.length === 0 || visibleCount > 0);
    }

    permissionSearch?.addEventListener('input', filterPermissions);
    document.getElementById('btn-clear-permission-search')?.addEventListener('click', function() {
        if (permissionSearch) {
            permissionSearch.value = '';
            filterPermissions();
            permissionSearch.focus();
        }
    });
    filterPermissions();

    function buildPermissionSlug(value) {
        return (value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }

    function syncPermissionSlug(force = false) {
        if (!permissionName || !permissionSlug) {
            return;
        }

        if (force || permissionFormMode?.value === 'create') {
            permissionSlug.value = buildPermissionSlug(permissionName.value);
        }
    }

    permissionName?.addEventListener('input', function() {
        syncPermissionSlug();
    });

    function prepareCreatePermissionModal() {
        if (!permissionForm) {
            return;
        }

        permissionForm.reset();
        permissionForm.action = permissionStoreUrl;
        permissionMethod.disabled = true;
        permissionFormMode.value = 'create';
        permissionId.value = '';
        syncPermissionSlug(true);
        permissionTitle.innerHTML = '<i class="fas fa-key"></i> Nouvelle permission';
        permissionSubmit.textContent = 'Créer';
    }

    document.getElementById('btn-create-permission')?.addEventListener('click', prepareCreatePermissionModal);

    document.querySelectorAll('.btn-edit-permission').forEach(button => {
        button.addEventListener('click', function() {
            if (!permissionForm || !permissionModal) {
                return;
            }

            permissionForm.reset();
            permissionForm.action = `${permissionUpdateBase}/${this.dataset.id}`;
            permissionMethod.disabled = false;
            permissionMethod.value = 'PUT';
            permissionFormMode.value = 'update';
            permissionId.value = this.dataset.id || '';
            permissionTitle.innerHTML = '<i class="fas fa-edit"></i> Modifier la permission';
            permissionSubmit.textContent = 'Enregistrer';

            permissionName.value = this.dataset.name || '';
            document.getElementById('permission_module').value = this.dataset.module || '';
            permissionSlug.value = this.dataset.slug || '';

            permissionModal.show();
        });
    });

    @if($errors->any() && old('permission_form_mode'))
        if (permissionModal) {
            @if(old('permission_form_mode') === 'update' && old('permission_id'))
                permissionForm.action = `${permissionUpdateBase}/{{ old('permission_id') }}`;
                permissionMethod.disabled = false;
                permissionMethod.value = 'PUT';
                permissionFormMode.value = 'update';
                permissionId.value = '{{ old('permission_id') }}';
                permissionTitle.innerHTML = '<i class="fas fa-edit"></i> Modifier la permission';
                permissionSubmit.textContent = 'Enregistrer';
            @else
                permissionForm.action = permissionStoreUrl;
                permissionMethod.disabled = true;
                permissionFormMode.value = 'create';
                permissionId.value = '';
                permissionTitle.innerHTML = '<i class="fas fa-key"></i> Nouvelle permission';
                permissionSubmit.textContent = 'Créer';
            @endif
            permissionModal.show();
        }
    @endif

    const btnCancel = document.getElementById('btn_cancel');
    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            if (userSelect) {
                userSelect.value = '';
            }
            setAllPermissions(false);
        });
    }
</script>
@endsection
