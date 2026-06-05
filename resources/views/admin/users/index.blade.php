@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('actions')
    @can('create_user')
        <button type="button" id="btnAddUser" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un utilisateur
        </button>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Liste des utilisateurs</h5>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Rechercher..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php
                        $currentRole = \App\Shared\Enums\UserRole::tryFrom(Auth::user()->role);
                        $targetRole = \App\Shared\Enums\UserRole::tryFrom($user->role);
                        $canToggleAccount = (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_user'))
                            && Auth::id() !== $user->id
                            && $targetRole
                            && !in_array($targetRole, [\App\Shared\Enums\UserRole::SUPERADMIN, \App\Shared\Enums\UserRole::ADMIN], true)
                            && ($currentRole === \App\Shared\Enums\UserRole::SUPERADMIN || ($currentRole && $currentRole->canManage($targetRole)));
                        $isAccountActive = (bool) $user->is_active && (string) $user->status === \App\Shared\Enums\UserStatus::ACTIVE->value;
                    @endphp
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role->color() ?? 'secondary' }}">
                                {{ $user->role->label() ?? $user->role }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->status->color() ?? 'secondary' }}">
                                {{ $user->status->label() ?? $user->status }}
                            </span>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="btn btn-outline-secondary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_user')
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-edit-user" 
                                            title="Modifier"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-phone="{{ $user->phone }}"
                                            data-specialite="{{ $user->specialite }}"
                                            data-diplome="{{ $user->diplome }}"
                                            data-adresse="{{ $user->adresse }}"
                                            data-role="{{ $user->role }}"
                                            data-status="{{ $user->status }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @if($canToggleAccount)
                                    <form method="POST"
                                          action="{{ $isAccountActive ? route('admin.users.deactivate', $user) : route('admin.users.activate', $user) }}"
                                          onsubmit="return confirm('{{ $isAccountActive ? 'Désactiver ce compte utilisateur ?' : 'Activer ce compte utilisateur ?' }}')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn {{ $isAccountActive ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $isAccountActive ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas {{ $isAccountActive ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>
                                @endif
                                @can('delete_user')
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-delete-user" 
                                            title="Supprimer"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- Modal unifié pour création/modification -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="user_id" id="user_id">
                
                <div class="modal-header text-white" style="background-color: var(--navbar-bg);">
                    <h5 class="modal-title" id="userModalLabel">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div id="modalAlert"></div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Prénom & Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>

                        <div class="col-12 d-none" id="formateurFields">
                            <div class="border rounded p-3">
                                <h6 class="mb-3">Informations formateur</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="specialite" class="form-label">Spécialité</label>
                                        <input type="text" class="form-control" id="specialite" name="specialite">
                                        <div class="invalid-feedback" id="specialite-error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="diplome" class="form-label">Diplôme</label>
                                        <select class="form-select" id="diplome" name="diplome">
                                            <option value="">-- Sélectionner --</option>
                                            @foreach(['DUT', 'BTS', 'LICENCE', 'MASTER', 'DEA', 'DOCTORAT', 'AUTRE'] as $diplome)
                                                <option value="{{ $diplome }}">{{ $diplome }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="diplome-error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="adresse" class="form-label">Adresse</label>
                                        <input type="text" class="form-control" id="adresse" name="adresse">
                                        <div class="invalid-feedback" id="adresse-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Sélectionner un rôle --</option>
                                @foreach($roles as $roleValue => $roleLabel)
                                    @if(auth()->user()->isSuperAdmin() || $roleValue !== 'superadmin')
                                        <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="role-error"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">-- Sélectionner un statut --</option>
                                @foreach($statuses as $statusValue => $statusLabel)
                                    <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="status-error"></div>
                        </div>
                        
                        <div class="col-md-6" id="passwordFields">
                            <label for="password" class="form-label" id="passwordLabel">Mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <button class="btn btn-outline-secondary password-toggle" type="button" data-target="password" aria-label="Afficher le mot de passe">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password-error"></div>
                            <small class="text-muted" id="passwordHelp">Laissez vide pour conserver le mot de passe actuel (en modification)</small>
                        </div>
                        
                        <div class="col-md-6" id="passwordConfirmField">
                            <label for="password_confirmation" class="form-label" id="passwordConfirmLabel">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <button class="btn btn-outline-secondary password-toggle" type="button" data-target="password_confirmation" aria-label="Afficher la confirmation du mot de passe">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password_confirmation-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="deleteUserName"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('userModalLabel');
    const submitBtn = document.getElementById('submitBtn');
    const roleSelect = document.getElementById('role');
    const formateurFields = document.getElementById('formateurFields');
    const formateurInputs = ['specialite', 'diplome', 'adresse']
        .map(id => document.getElementById(id))
        .filter(Boolean);
    const roleFormateur = "{{ \App\Shared\Enums\UserRole::FORMATEUR->value }}";
    
    // URLs
    const storeUrl = "{{ route('admin.users.store') }}";
    const updateBase = "{{ url('admin/users') }}";
    
    // Ajouter un utilisateur
    document.getElementById('btnAddUser')?.addEventListener('click', function() {
        resetForm();
        userForm.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        modalTitle.textContent = 'Ajouter un utilisateur';
        
        // Rendre le mot de passe obligatoire en création
        document.getElementById('password').required = true;
        document.getElementById('password_confirmation').required = true;
        document.getElementById('passwordLabel').innerHTML = 'Mot de passe <span class="text-danger">*</span>';
        document.getElementById('passwordConfirmLabel').innerHTML = 'Confirmer le mot de passe <span class="text-danger">*</span>';
        document.getElementById('passwordHelp').style.display = 'none';
        toggleFormateurFields();
        
        userModal.show();
    });
    
    // Modifier un utilisateur
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            resetForm();
            
            // Remplir le formulaire
            document.getElementById('name').value = this.dataset.name || '';
            document.getElementById('email').value = this.dataset.email || '';
            document.getElementById('phone').value = this.dataset.phone || '';
            document.getElementById('specialite').value = this.dataset.specialite || '';
            document.getElementById('diplome').value = this.dataset.diplome || '';
            document.getElementById('adresse').value = this.dataset.adresse || '';
            document.getElementById('role').value = this.dataset.role || '';
            document.getElementById('status').value = this.dataset.status || '';
            document.getElementById('user_id').value = id;
            
            // Configuration pour modification
            userForm.action = updateBase + '/' + id;
            document.getElementById('formMethod').value = 'PUT';
            modalTitle.textContent = 'Modifier l\'utilisateur';
            
            // Mot de passe optionnel en modification
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            document.getElementById('passwordLabel').innerHTML = 'Mot de passe <span class="text-muted">(optionnel)</span>';
            document.getElementById('passwordConfirmLabel').innerHTML = 'Confirmer le mot de passe';
            document.getElementById('passwordHelp').style.display = 'block';
            toggleFormateurFields();
            
            userModal.show();
        });
    });

    roleSelect?.addEventListener('change', toggleFormateurFields);

    document.querySelectorAll('.password-toggle').forEach(button => {
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
    
    // Supprimer un utilisateur
    document.querySelectorAll('.btn-delete-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            document.getElementById('deleteUserName').textContent = name;
            document.getElementById('deleteForm').action = updateBase + '/' + id;
            deleteModal.show();
        });
    });
    
    // Soumission du formulaire avec AJAX pour une meilleure UX
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Nettoyer les erreurs précédentes
        clearErrors();
        
        const formData = new FormData(this);
        const method = document.getElementById('formMethod').value;
        
        // Ajouter _method pour PUT si nécessaire
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Succès - recharger la page ou mettre à jour le tableau
                window.location.reload();
            } else {
                // Erreurs de validation
                if (data.errors) {
                    displayErrors(data.errors);
                } else {
                    showAlert('error', data.message || 'Une erreur est survenue');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Erreur lors de la communication avec le serveur');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
        });
    });
    
    function resetForm() {
        userForm.reset();
        document.getElementById('user_id').value = '';
        clearErrors();
        document.getElementById('modalAlert').innerHTML = '';
        toggleFormateurFields();
    }

    function toggleFormateurFields() {
        const isFormateur = roleSelect?.value === roleFormateur;
        formateurFields?.classList.toggle('d-none', !isFormateur);
        formateurInputs.forEach(input => input.required = isFormateur);
    }
    
    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }
    
    function displayErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.getElementById(field);
            const errorDiv = document.getElementById(`${field}-error`);
            
            if (input && errorDiv) {
                input.classList.add('is-invalid');
                errorDiv.textContent = messages[0];
            }
        }
    }
    
    function showAlert(type, message) {
        const alertDiv = document.getElementById('modalAlert');
        const alertClass = type === 'error' ? 'danger' : type;
        alertDiv.innerHTML = `
            <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
});
</script>
@endsection
