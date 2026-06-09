@extends('layouts.admin')

@section('title', 'Mise à jour des grades')

@section('actions')
    <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour aux cartes
    </a>
@endsection

@section('content')
<div class="grade-page">
<div class="card border-0 shadow-sm mb-4 grade-filter-card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-layer-group me-2 text-success"></i> Passage de grade en groupe</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.cards.grade-updates') }}" class="row g-2 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label class="form-label small text-muted" for="search">Recherche</label>
                <input type="search" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nom, prénom ou licence">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted" for="salle">Salle</label>
                <select name="salle" id="salle" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($salles as $salle)
                        <option value="{{ $salle }}" {{ request('salle') === $salle ? 'selected' : '' }}>{{ $salle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted" for="section">Section</label>
                <select name="section" id="section" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ request('section') === $section ? 'selected' : '' }}>{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted" for="grade">Grade actuel</label>
                <select name="grade" id="grade" class="form-select">
                    <option value="">Tous</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}" {{ request('grade') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 d-grid">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="{{ route('admin.cards.grade-updates.apply') }}" id="gradeUpdateForm">
    @csrf
    @foreach(['search', 'salle', 'section', 'grade'] as $filter)
        @if(request()->filled($filter))
            <input type="hidden" name="{{ $filter }}" value="{{ request($filter) }}">
        @endif
    @endforeach

    <div class="card border-0 shadow-sm grade-list-card">
        <div class="card-header bg-white py-3">
            <div class="grade-toolbar">
                <div>
                    <h5 class="mb-1"><i class="fas fa-user-check me-2 text-success"></i> Cartes à mettre à jour</h5>
                    <div class="text-muted small">{{ $licenceHolders->total() }} carte(s) trouvée(s)</div>
                </div>
                <div class="grade-actions">
                    <select name="update_mode" id="update_mode" class="form-select">
                        <option value="next" {{ old('update_mode', 'next') === 'next' ? 'selected' : '' }}>Appliquer le grade suivant</option>
                        <option value="custom" {{ old('update_mode') === 'custom' ? 'selected' : '' }}>Appliquer un grade choisi</option>
                    </select>
                    <select name="target_grade" id="target_grade" class="form-select">
                        <option value="">Grade à appliquer</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade }}" {{ old('target_grade') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                        @endforeach
                    </select>
                    <label class="form-check grade-check mb-0">
                        <input class="form-check-input" type="checkbox" name="refresh_issued_at" value="1" {{ old('refresh_issued_at') ? 'checked' : '' }}>
                        <span class="form-check-label">Actualiser la date</span>
                    </label>
                    <label class="form-check grade-check grade-select-all-mobile mb-0">
                        <input class="form-check-input select-all-cards" type="checkbox" checked>
                        <span class="form-check-label">Tout sélectionner</span>
                    </label>
                    <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-1"></i> Mettre à jour
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 grade-update-table">
                <thead class="table-light">
                    <tr>
                        <th class="select-col">
                            <input type="checkbox" class="form-check-input select-all-cards" id="selectAllCards" checked>
                        </th>
                        <th>Disciple</th>
                        <th>Licence</th>
                        <th>Section / Salle</th>
                        <th>Grade actuel</th>
                        <th>Grade suivant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licenceHolders as $holder)
                        @php($nextGrade = $nextGrades[$holder->grade] ?? null)
                        <tr>
                            <td class="select-col">
                                <input type="checkbox" class="form-check-input holder-checkbox" name="holder_ids[]" value="{{ $holder->id }}" checked>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $holder->full_name }}</div>
                                <div class="text-muted small">{{ $holder->gender === 'F' ? 'Féminin' : 'Masculin' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $holder->licence_number }}</span></td>
                            <td>
                                <div>{{ $holder->club ?? '-' }}</div>
                                <div class="text-muted small">{{ $holder->salle ?? '-' }}</div>
                            </td>
                            <td>{{ $holder->grade ?? '-' }}</td>
                            <td>
                                @if($nextGrade && $nextGrade !== 'Aucun grade supérieur')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $nextGrade }}</span>
                                @else
                                    <span class="text-muted">Aucun grade supérieur</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-id-card d-block mb-3" style="font-size: 42px; opacity: .2;"></i>
                                Aucune carte trouvée pour ces filtres.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grade-mobile-list">
            @forelse($licenceHolders as $holder)
                @php($nextGrade = $nextGrades[$holder->grade] ?? null)
                <article class="grade-mobile-card">
                    <label class="grade-mobile-select">
                        <input type="checkbox" class="form-check-input holder-checkbox" name="holder_ids[]" value="{{ $holder->id }}" checked>
                        <span>Sélectionner</span>
                    </label>
                    <div class="grade-mobile-main">
                        <div class="grade-mobile-name">{{ $holder->full_name }}</div>
                        <span class="badge bg-light text-dark border">{{ $holder->licence_number }}</span>
                    </div>
                    <div class="grade-mobile-meta">
                        <div>
                            <span>Section</span>
                            <strong>{{ $holder->club ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Salle</span>
                            <strong>{{ $holder->salle ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Grade actuel</span>
                            <strong>{{ $holder->grade ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Grade suivant</span>
                            @if($nextGrade && $nextGrade !== 'Aucun grade supérieur')
                                <strong class="text-success">{{ $nextGrade }}</strong>
                            @else
                                <strong class="text-muted">Aucun</strong>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="grade-mobile-empty text-center py-5 text-muted">
                    <i class="fas fa-id-card d-block mb-3" style="font-size: 42px; opacity: .2;"></i>
                    Aucune carte trouvée pour ces filtres.
                </div>
            @endforelse
        </div>

        @if($licenceHolders->hasPages())
            <div class="card-footer bg-white">
                {{ $licenceHolders->links() }}
            </div>
        @endif
    </div>
</form>
</div>

<style>
    .grade-page {
        width: 100%;
    }

    .grade-toolbar,
    .grade-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .grade-toolbar {
        justify-content: space-between;
    }

    .grade-actions {
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .grade-actions .form-select {
        width: auto;
        min-width: 210px;
    }

    .grade-check {
        display: flex;
        align-items: center;
        gap: 7px;
        min-height: 38px;
        padding: 0 8px;
        white-space: nowrap;
    }

    .select-col {
        width: 44px;
        text-align: center;
    }

    .grade-update-table td,
    .grade-update-table th {
        overflow-wrap: anywhere;
    }

    .grade-mobile-list {
        display: none;
    }

    .grade-select-all-mobile {
        display: none;
    }

    .grade-mobile-card {
        border-bottom: 1px solid #edf0f3;
        padding: 14px 16px;
    }

    .grade-mobile-card:last-child {
        border-bottom: 0;
    }

    .grade-mobile-select {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: .85rem;
        margin-bottom: 10px;
    }

    .grade-mobile-main {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .grade-mobile-name {
        font-weight: 700;
        line-height: 1.25;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .grade-mobile-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .grade-mobile-meta div {
        background: #f8f9fa;
        border: 1px solid #edf0f3;
        border-radius: 8px;
        padding: 9px 10px;
        min-width: 0;
    }

    .grade-mobile-meta span {
        color: #6c757d;
        display: block;
        font-size: .75rem;
        margin-bottom: 3px;
    }

    .grade-mobile-meta strong {
        display: block;
        font-size: .9rem;
        overflow-wrap: anywhere;
    }

    @media (max-width: 991.98px) {
        .grade-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .grade-actions {
            justify-content: stretch;
        }

        .grade-actions .form-select,
        .grade-actions .btn {
            flex: 1 1 220px;
        }
    }

    @media (max-width: 575.98px) {
        .page-actions,
        .page-actions .btn {
            width: 100%;
        }

        .page-actions .btn {
            justify-content: center;
        }

        .grade-filter-card .card-body,
        .grade-list-card .card-header {
            padding: 14px;
        }

        .grade-filter-card .form-label {
            margin-bottom: 4px;
        }

        .grade-filter-card .form-control,
        .grade-filter-card .form-select,
        .grade-filter-card .btn,
        .grade-actions .form-select,
        .grade-actions .btn {
            min-height: 42px;
        }

        .grade-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .grade-actions .form-select,
        .grade-actions .btn,
        .grade-check {
            width: 100%;
            min-width: 0;
        }

        .grade-check {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            justify-content: flex-start;
            padding: 8px 10px;
        }
    }

    @media (max-width: 767.98px) {
        .grade-list-card .table-responsive {
            display: none;
        }

        .grade-mobile-list {
            display: block;
        }

        .grade-select-all-mobile {
            display: flex;
        }

        .grade-mobile-main .badge {
            flex: 0 0 auto;
            max-width: 45%;
            white-space: normal;
        }
    }

    @media (max-width: 380px) {
        .grade-mobile-meta {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllControls = Array.from(document.querySelectorAll('.select-all-cards'));
    const checkboxes = Array.from(document.querySelectorAll('.holder-checkbox'));
    const updateMode = document.getElementById('update_mode');
    const targetGrade = document.getElementById('target_grade');
    const gradeUpdateForm = document.getElementById('gradeUpdateForm');
    const mobileQuery = window.matchMedia('(max-width: 767.98px)');

    function activeCheckboxes() {
        return checkboxes.filter(checkbox => !checkbox.disabled);
    }

    function syncEnabledCheckboxes() {
        const isMobile = mobileQuery.matches;

        checkboxes.forEach(function (checkbox) {
            checkbox.disabled = isMobile
                ? !checkbox.closest('.grade-mobile-list')
                : !!checkbox.closest('.grade-mobile-list');
        });
    }

    function syncSelectAll() {
        const enabledCheckboxes = activeCheckboxes();
        const values = [...new Set(enabledCheckboxes.map(checkbox => checkbox.value))];
        const checkedValues = values.filter(value => enabledCheckboxes.some(checkbox => checkbox.value === value && checkbox.checked));

        selectAllControls.forEach(function (control) {
            control.checked = values.length > 0 && checkedValues.length === values.length;
            control.indeterminate = checkedValues.length > 0 && !control.checked;
        });
    }

    function syncTargetGrade() {
        if (!updateMode || !targetGrade) return;
        targetGrade.disabled = updateMode.value !== 'custom';
        targetGrade.required = updateMode.value === 'custom';
    }

    selectAllControls.forEach(control => control.addEventListener('change', function () {
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        syncSelectAll();
    }));

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', function () {
        checkboxes
            .filter(item => item.value === this.value)
            .forEach(item => item.checked = this.checked);
        syncSelectAll();
    }));
    updateMode?.addEventListener('change', syncTargetGrade);
    mobileQuery.addEventListener?.('change', function () {
        syncEnabledCheckboxes();
        syncSelectAll();
    });
    gradeUpdateForm?.addEventListener('submit', syncEnabledCheckboxes);

    syncEnabledCheckboxes();
    syncSelectAll();
    syncTargetGrade();
});
</script>
@endsection
