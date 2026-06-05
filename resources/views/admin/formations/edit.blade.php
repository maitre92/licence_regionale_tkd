@extends('layouts.admin')

@section('title', 'Modifier la Formation')
@php $page_title = 'Modifier: ' . $formation->code; @endphp

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.formations.show', $formation) }}" class="btn btn-outline-info shadow-sm">
            <i class="fas fa-eye me-1"></i> Voir détails
        </a>
        <a href="{{ route('admin.formations.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <form action="{{ route('admin.formations.update', $formation) }}" method="POST" id="formationForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Informations principales -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Informations principales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nom de la formation <span class="text-danger">*</span></label>
                                <input type="text" name="nom" id="formation_nom" class="form-control" value="{{ old('nom', $formation->nom) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Code formation <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="code" id="formation_code" class="form-control" value="{{ old('code', $formation->code) }}" required>
                                    <button class="btn btn-outline-secondary" type="button" id="generateCodeBtn">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Catégorie <span class="text-danger">*</span></label>
                                <select name="categorie_formation_id" class="form-select" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categorie_formation_id', $formation->categorie_formation_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $formation->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations pédagogiques -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                        <h5 class="card-title mb-0"><i class="fas fa-graduation-cap me-2"></i> Informations pédagogiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Type de formation <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="Présentiel" {{ old('type', $formation->type) == 'Présentiel' ? 'selected' : '' }}>Présentiel</option>
                                    <option value="En ligne" {{ old('type', $formation->type) == 'En ligne' ? 'selected' : '' }}>En ligne</option>
                                    <option value="Hybride" {{ old('type', $formation->type) == 'Hybride' ? 'selected' : '' }}>Hybride</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Niveau</label>
                                @php $selectedNiveau = old('niveau', $formation->niveau); @endphp
                                <select name="niveau" class="form-select">
                                    <option value="">Sélectionner un niveau...</option>
                                    @foreach(['Débutant', 'Intermédiaire', 'Avancé'] as $niveau)
                                        <option value="{{ $niveau }}" {{ $selectedNiveau === $niveau ? 'selected' : '' }}>{{ $niveau }}</option>
                                    @endforeach
                                    @if($selectedNiveau && !in_array($selectedNiveau, ['Débutant', 'Intermédiaire', 'Avancé'], true))
                                        <option value="{{ $selectedNiveau }}" selected>{{ $selectedNiveau }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Durée (Heures)</label>
                                <div class="input-group">
                                    <input type="number" name="duree_heures" class="form-control" value="{{ old('duree_heures', $formation->duree_heures) }}" min="0">
                                    <span class="input-group-text">h</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coût (FCFA)</label>
                                <div class="input-group">
                                    <input type="number" name="cout" class="form-control" value="{{ old('cout', $formation->cout) }}" min="0">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Frais d'inscription (FCFA)</label>
                                <div class="input-group">
                                    <input type="number" name="frais_inscription" class="form-control" value="{{ old('frais_inscription', $formation->frais_inscription) }}" min="0">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Capacité Max</label>
                                <div class="input-group">
                                    <input type="number" name="capacite_max" class="form-control" value="{{ old('capacite_max', $formation->capacite_max) }}" min="0">
                                    <span class="input-group-text">élèves</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Organisation -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                        <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i> Organisation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Salle / Lieu</label>
                                <input type="text" name="salle" class="form-control" value="{{ old('salle', $formation->salle) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date début</label>
                                <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut', $formation->date_debut ? $formation->date_debut->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date fin</label>
                                <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin', $formation->date_fin ? $formation->date_fin->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0">Formateurs affectés</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showAllRoles">
                                        <label class="form-check-label small" for="showAllRoles">Tous les rôles</label>
                                    </div>
                                </div>
                                <select name="formateurs[]" id="formateurs_select" class="form-control" multiple>
                                    @foreach($formateurs as $formateur)
                                        <option value="{{ $formateur->id }}" 
                                                data-role="{{ $formateur->role }}"
                                                {{ in_array($formateur->id, old('formateurs', $selectedFormateurs)) ? 'selected' : '' }}>
                                            {{ $formateur->name }} ({{ $formateur->role }})
                                        </option>
                                    @endforeach
                                </select>
                                <div id="formateur_percentage_inputs"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn text-white py-3 shadow" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-2"></i> Mettre à jour la formation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="formateurPercentageModal" tabindex="-1" aria-labelledby="formateurPercentageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="modal-title" id="formateurPercentageModalLabel">Commission du formateur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Sélectionnez le pourcentage de commission pour <strong id="percentageFormateurName"></strong>.</p>
                <select id="formateurPercentageSelect" class="form-select">
                    <option value="">-- Choisir un pourcentage --</option>
                    @foreach([20, 30, 40, 50, 60, 70] as $percentage)
                        <option value="{{ $percentage }}">{{ $percentage }}%</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Le pourcentage de commission est obligatoire.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveFormateurPercentage">Valider</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Choices.js with filtering
    const selectElement = document.getElementById('formateurs_select');
    const allOptions = Array.from(selectElement.options).map(opt => ({
        value: opt.value,
        label: opt.text,
        role: opt.dataset.role,
        selected: opt.selected
    }));
    const selectedPercentages = @json(old('formateur_commissions', $selectedPourcentages ?? []));
    const percentageInputs = document.getElementById('formateur_percentage_inputs');
    const percentageModalElement = document.getElementById('formateurPercentageModal');
    const percentageModal = percentageModalElement ? new bootstrap.Modal(percentageModalElement) : null;
    const percentageSelect = document.getElementById('formateurPercentageSelect');
    const percentageFormateurName = document.getElementById('percentageFormateurName');
    const savePercentageButton = document.getElementById('saveFormateurPercentage');
    let pendingPercentageFormateur = null;

    let choicesInstance = new Choices(selectElement, {
        removeItemButton: true,
        placeholderValue: 'Sélectionner des formateurs',
    });

    const showAllCheckbox = document.getElementById('showAllRoles');
    const roleFormateur = "{{ \App\Shared\Enums\UserRole::FORMATEUR->value }}";

    function updateTrainerList() {
        const showAll = showAllCheckbox.checked;
        const filteredChoices = allOptions.filter(opt => {
            if (opt.selected) return true;
            return showAll || opt.role === roleFormateur;
        });

        choicesInstance.destroy();
        selectElement.innerHTML = '';
        filteredChoices.forEach(opt => {
            const newOpt = new Option(opt.label, opt.value, opt.selected, opt.selected);
            newOpt.dataset.role = opt.role;
            selectElement.add(newOpt);
        });

        choicesInstance = new Choices(selectElement, {
            removeItemButton: true,
            placeholderValue: 'Sélectionner des formateurs',
        });
        renderPercentageInputs();
    }

    showAllCheckbox.addEventListener('change', updateTrainerList);
    updateTrainerList();

    selectElement.addEventListener('change', function() {
        const selectedValues = Array.from(selectElement.selectedOptions).map(opt => opt.value);
        allOptions.forEach(opt => opt.selected = selectedValues.includes(opt.value));
        renderPercentageInputs();
    });

    selectElement.addEventListener('addItem', function(event) {
        const value = String(event.detail.value);
        const option = allOptions.find(opt => opt.value === value);

        if (option?.role === roleFormateur && !selectedPercentages[value]) {
            pendingPercentageFormateur = option;
            percentageFormateurName.textContent = option.label.replace(/\s*\([^)]*\)\s*$/, '');
            percentageSelect.value = '';
            percentageModal?.show();
        }
    });

    savePercentageButton?.addEventListener('click', function() {
        if (!pendingPercentageFormateur || !percentageSelect.value) {
            percentageSelect.classList.add('is-invalid');
            return;
        }

        selectedPercentages[pendingPercentageFormateur.value] = percentageSelect.value;
        percentageSelect.classList.remove('is-invalid');
        pendingPercentageFormateur = null;
        renderPercentageInputs();
        percentageModal?.hide();
    });

    function renderPercentageInputs() {
        if (!percentageInputs) {
            return;
        }

        const selectedValues = Array.from(selectElement.selectedOptions).map(opt => String(opt.value));
        percentageInputs.innerHTML = '';

        selectedValues.forEach(value => {
            if (!selectedPercentages[value]) {
                return;
            }

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `formateur_commissions[${value}]`;
            input.value = selectedPercentages[value];
            percentageInputs.appendChild(input);
        });
    }

    // 2. Code Generation
    const nomInput = document.getElementById('formation_nom');
    const codeInput = document.getElementById('formation_code');
    const genBtn = document.getElementById('generateCodeBtn');

    genBtn.addEventListener('click', function() {
        if (nomInput.value) {
            let slug = nomInput.value.toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^A-Z0-9]/g, '-').replace(/-+/g, '-').substring(0, 10);
            let random = Math.floor(100 + Math.random() * 900);
            codeInput.value = slug + '-' + random;
        }
    });

    const form = document.getElementById('formationForm');
    form.onsubmit = function() {
        const missingCommission = Array.from(selectElement.selectedOptions).find(opt => {
            return opt.dataset.role === roleFormateur && !selectedPercentages[String(opt.value)];
        });

        if (missingCommission) {
            pendingPercentageFormateur = allOptions.find(opt => opt.value === String(missingCommission.value));
            percentageFormateurName.textContent = missingCommission.text.replace(/\s*\([^)]*\)\s*$/, '');
            percentageSelect.value = '';
            percentageSelect.classList.remove('is-invalid');
            percentageModal?.show();
            return false;
        }

        return true;
    };
});
</script>

<style>
    .choices__inner { border-radius: 8px !important; }
    .choices__list--multiple .choices__item { background-color: var(--navbar-bg) !important; border: 1px solid var(--navbar-bg) !important; }
</style>
@endsection
