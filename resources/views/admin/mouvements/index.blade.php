@extends('layouts.admin')

@section('title', 'Mouvements / Pilotage')

@section('content')
@php
    $periodLabels = [
        'today' => 'Aujourd’hui',
        'week' => 'Cette semaine',
        'month' => 'Ce mois',
        'year' => 'Cette année',
        'custom' => 'Période personnalisée',
    ];

    $money = fn($value) => number_format((float) $value, 0, ',', ' ');
    $periodLabel = $periodLabels[$filters['period']] ?? 'Ce mois';
    $statusClass = fn($value) => match ($value) {
        'Terminé' => 'success',
        'En cours' => 'primary',
        'Suspendu' => 'warning',
        default => 'secondary',
    };
@endphp

<div class="card border-0 shadow-sm mb-4 pilotage-filter-card">
    <div class="card-body p-3 p-lg-4">
        <form method="GET" action="{{ route('admin.mouvements.index') }}" class="row g-3 align-items-end" id="movementFilters">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <div>
                        <div class="small fw-bold text-muted text-uppercase mb-1">Période analysée</div>
                        <div class="fw-bold text-dark">{{ $periodLabel }} · {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted">Période</label>
                <select name="period" id="periodSelect" class="form-select">
                    @foreach($periodLabels as $key => $label)
                        <option value="{{ $key }}" @selected($filters['period'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6 custom-period-field">
                <label class="form-label small fw-bold text-muted">Date début</label>
                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
            </div>

            <div class="col-lg-3 col-md-6 custom-period-field">
                <label class="form-label small fw-bold text-muted">Date fin</label>
                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted">Formation</label>
                <select name="formation_id" id="formationSelect" class="form-select">
                    <option value="">Toutes les formations</option>
                    @foreach($formations as $formation)
                        <option value="{{ $formation->id }}" @selected((int) $filters['formation_id'] === $formation->id)>
                            {{ $formation->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted">Groupe de formation</label>
                <select name="groupe_formation_id" id="groupeSelect" class="form-select">
                    <option value="">Tous les groupes</option>
                    @foreach($groupes as $groupe)
                        <option value="{{ $groupe->id }}" data-formation-id="{{ $groupe->formation_id }}" @selected((int) $filters['groupe_formation_id'] === $groupe->id)>
                            {{ $groupe->nom }}{{ $groupe->formation ? ' · ' . $groupe->formation->nom : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted d-none d-md-block">&nbsp;</label>
                <a href="{{ route('admin.mouvements.index') }}" class="btn btn-light border w-100">
                    <i class="fas fa-rotate-left me-1"></i> Réinitialiser
                </a>
            </div>

        </form>
    </div>
</div>

@if(!$hasActiveFilter)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light text-primary" style="width: 64px; height: 64px;">
                <i class="fas fa-filter fa-lg"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Sélectionnez une formation ou un groupe de formation</h5>
            <p class="text-muted mb-0">
                Utilisez les filtres ci-dessus pour charger le tableau récapitulatif des mouvements financiers, apprenants et pédagogiques.
            </p>
        </div>
    </div>
@else
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper"><i class="fas fa-coins"></i></div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small">Recettes</span>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Recettes encaissées</h6>
                <h3 class="fw-bold mb-0 counter-value">{{ $money($summary['recettes']) }} <small>FCFA</small></h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper"><i class="fas fa-receipt"></i></div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small">Dépenses</span>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Charges enregistrées</h6>
                <h3 class="fw-bold mb-0 counter-value">{{ $money($summary['depenses']) }} <small>FCFA</small></h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper"><i class="fas fa-scale-balanced"></i></div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small">Solde</span>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Résultat net</h6>
                <h3 class="fw-bold mb-0 counter-value">{{ $money($summary['solde']) }} <small>FCFA</small></h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper"><i class="fas fa-layer-group"></i></div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small">Groupes</span>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Groupes sur la période</h6>
                <h3 class="fw-bold mb-0 counter-value">{{ $summary['groupes_actifs_periode'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100 mini-kpi">
            <div class="card-body">
                <div class="small text-muted fw-bold">Formés</div>
                <div class="h4 fw-bold mb-0">{{ $summary['apprenants_formes'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100 mini-kpi">
            <div class="card-body">
                <div class="small text-muted fw-bold">Inscrits période</div>
                <div class="h4 fw-bold mb-0">{{ $summary['apprenants_inscrits'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100 mini-kpi">
            <div class="card-body">
                <div class="small text-muted fw-bold">Recouvrement</div>
                <div class="h4 fw-bold mb-0">{{ $summary['taux_recouvrement'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100 mini-kpi">
            <div class="card-body">
                <div class="small text-muted fw-bold">Présence</div>
                <div class="h4 fw-bold mb-0">{{ $summary['taux_presence'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100 mini-kpi">
            <div class="card-body">
                <div class="small text-muted fw-bold">Moyenne</div>
                <div class="h4 fw-bold mb-0">{{ $summary['moyenne_notes'] }}/20</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100 mini-kpi">
            <div class="card-body">
                <div class="small text-muted fw-bold">Attestations</div>
                <div class="h4 fw-bold mb-0">{{ $summary['attestations'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center gap-3 flex-wrap border-0">
        <div>
            <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-table-list text-primary me-2"></i> Tableau récapitulatif par groupe de formation</h6>
            <div class="small text-muted">Synthèse financière, apprenants et pédagogie selon les filtres appliqués.</div>
        </div>
        <span class="badge bg-light text-dark border">{{ $recapRows->count() }} groupe(s)</span>
    </div>
    <div class="table-responsive recap-table-wrapper">
        <table class="table table-hover table-bordered align-middle mb-0 recap-table">
            <thead class="bg-light">
                <tr>
                    <th>Formation / Groupe</th>
                    <th>Statut</th>
                    <th class="text-end">Inscrits</th>
                    <th class="text-end">Formés</th>
                    <th class="text-end">Recettes</th>
                    <th class="text-end">Rémun. form.</th>
                    <th class="text-end">Autres dép.</th>
                    <th class="text-end">Dépenses tot.</th>
                    <th class="text-end">Solde</th>
                    <th class="text-end">Reste</th>
                    <th class="text-end">Recouv.</th>
                    <th class="text-end">Prés.</th>
                    <th class="text-end">Moy.</th>
                    <th class="text-end">Réussite</th>
                    <th class="text-end">Éval.</th>
                    <th class="text-end">Exam.</th>
                    <th class="text-end">Attest.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recapRows as $row)
                    <tr>
                        <td class="min-col-title">
                            <div class="fw-bold text-dark">{{ $row['groupe'] }}</div>
                            <div class="text-muted small">{{ $row['formation'] }} · {{ $row['code'] }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                {{ $row['date_debut']?->format('d/m/Y') ?? 'Date non définie' }}
                                -
                                {{ $row['date_fin']?->format('d/m/Y') ?? 'Date non définie' }}
                                @if($row['formateur'])
                                    · {{ $row['formateur'] }}
                                @endif
                            </div>
                        </td>
                        <td><span class="badge bg-{{ $statusClass($row['statut']) }} bg-opacity-10 text-{{ $statusClass($row['statut']) }} border-0">{{ $row['statut'] }}</span></td>
                        <td class="text-end">
                            <div class="fw-bold">{{ $row['inscrits_total'] }}</div>
                            <div class="small text-muted">+{{ $row['inscrits_periode'] }}</div>
                        </td>
                        <td class="text-end fw-bold">{{ $row['formes'] }}</td>
                        <td class="text-end fw-bold text-success">{{ $money($row['recettes']) }}</td>
                        <td class="text-end fw-bold text-warning">{{ $money($row['remuneration_formateurs']) }}</td>
                        <td class="text-end fw-bold text-danger">{{ $money($row['autres_depenses']) }}</td>
                        <td class="text-end fw-bold text-danger">{{ $money($row['depenses']) }}</td>
                        <td class="text-end fw-bold {{ $row['solde'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $money($row['solde']) }}</td>
                        <td class="text-end fw-bold">{{ $money($row['reste_a_payer']) }}</td>
                        <td class="text-end">{{ $row['taux_recouvrement'] }}%</td>
                        <td class="text-end">{{ $row['taux_presence'] }}%</td>
                        <td class="text-end">{{ $row['moyenne_notes'] }}/20</td>
                        <td class="text-end">{{ $row['taux_reussite'] }}%</td>
                        <td class="text-end">{{ $row['evaluations'] }}</td>
                        <td class="text-end">{{ $row['examens'] }}</td>
                        <td class="text-end">{{ $row['attestations'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="17" class="text-center text-muted py-5">Aucun groupe de formation trouvé pour les filtres sélectionnés.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($recapRows->isNotEmpty())
                <tfoot class="bg-light">
                    <tr>
                        <th>Total</th>
                        <th></th>
                        <th class="text-end">{{ $recapRows->sum('inscrits_total') }}</th>
                        <th class="text-end">{{ $recapRows->sum('formes') }}</th>
                        <th class="text-end text-success">{{ $money($recapRows->sum('recettes')) }}</th>
                        <th class="text-end text-warning">{{ $money($recapRows->sum('remuneration_formateurs')) }}</th>
                        <th class="text-end text-danger">{{ $money($recapRows->sum('autres_depenses')) }}</th>
                        <th class="text-end text-danger">{{ $money($recapRows->sum('depenses')) }}</th>
                        <th class="text-end {{ $recapRows->sum('solde') >= 0 ? 'text-success' : 'text-danger' }}">{{ $money($recapRows->sum('solde')) }}</th>
                        <th class="text-end">{{ $money($recapRows->sum('reste_a_payer')) }}</th>
                        <th colspan="7"></th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-credit-card text-success me-2"></i> Derniers paiements</h6>
                <span class="badge bg-success bg-opacity-10 text-success border-0">{{ $money($summary['recettes']) }} FCFA</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small">Reçu</th>
                            <th class="border-0 small">Apprenant</th>
                            <th class="border-0 small">Date</th>
                            <th class="border-0 small text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($financial['recent_payments'] as $payment)
                            <tr>
                                <td class="small fw-bold">{{ $payment->recu_numero }}</td>
                                <td>
                                    <div class="small fw-bold">{{ $payment->inscription?->apprenant?->nom_complet ?? 'Apprenant supprimé' }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $payment->inscription?->groupeFormation?->nom ?? $payment->inscription?->formation?->nom }}</div>
                                </td>
                                <td class="small">{{ $payment->date_paiement?->format('d/m/Y') }}</td>
                                <td class="text-end fw-bold text-success small">{{ $money($payment->montant) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Aucun paiement sur cette période</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar text-danger me-2"></i> Dernières dépenses</h6>
                <span class="badge bg-danger bg-opacity-10 text-danger border-0">{{ $money($summary['depenses']) }} FCFA</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small">Catégorie</th>
                            <th class="border-0 small">Titre</th>
                            <th class="border-0 small">Date</th>
                            <th class="border-0 small text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($financial['recent_expenses'] as $expense)
                            <tr>
                                <td><span class="badge bg-light text-dark border small">{{ $expense->categorie }}</span></td>
                                <td>
                                    <div class="small fw-bold">{{ $expense->titre }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $expense->groupeFormation?->nom ?? $expense->formation?->nom ?? $expense->beneficiaire }}</div>
                                </td>
                                <td class="small">{{ $expense->date_depense?->format('d/m/Y') }}</td>
                                <td class="text-end fw-bold text-danger small">{{ $money($expense->montant) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Aucune dépense sur cette période</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    .pilotage-filter-card {
        border-radius: 12px;
    }

    .stat-card {
        border-radius: 16px;
        position: relative;
        z-index: 1;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        top: -15%;
        right: -10%;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: -1;
    }

    .stat-icon-wrapper {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .counter-value {
        font-size: 1.35rem;
        letter-spacing: 0;
    }

    .counter-value small {
        font-size: 0.68rem;
    }

    .mini-kpi {
        border-radius: 12px;
    }

    .recap-table-wrapper {
        max-height: 620px;
    }

    .recap-table {
        min-width: 1360px;
    }

    .recap-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #f8f9fa;
    }

    .min-col-title {
        min-width: 260px;
    }

    .table thead th,
    .table tfoot th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 12px 16px;
        white-space: nowrap;
    }

    .table td {
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .counter-value {
            font-size: 1.18rem;
        }

        .pilotage-filter-card .btn,
        .pilotage-filter-card .form-select,
        .pilotage-filter-card .form-control {
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('movementFilters');
    const periodSelect = document.getElementById('periodSelect');
    const customFields = document.querySelectorAll('.custom-period-field');
    const formationSelect = document.getElementById('formationSelect');
    const groupeSelect = document.getElementById('groupeSelect');
    const groupOptions = Array.from(groupeSelect.options);
    const startDateInput = form.querySelector('[name="start_date"]');
    const endDateInput = form.querySelector('[name="end_date"]');
    let submitTimer = null;

    function submitFilters() {
        clearTimeout(submitTimer);
        submitTimer = setTimeout(() => form.requestSubmit(), 120);
    }

    function syncCustomFields() {
        customFields.forEach((field) => {
            field.style.display = periodSelect.value === 'custom' ? '' : 'none';
        });
    }

    function syncGroupOptions() {
        const selectedFormation = formationSelect.value;
        const currentValue = groupeSelect.value;
        let currentStillVisible = currentValue === '';

        groupOptions.forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const matches = selectedFormation === '' || option.dataset.formationId === selectedFormation;
            option.hidden = !matches;
            option.disabled = !matches;

            if (matches && option.value === currentValue) {
                currentStillVisible = true;
            }
        });

        if (!currentStillVisible) {
            groupeSelect.value = '';
        }
    }

    periodSelect.addEventListener('change', function() {
        syncCustomFields();
        if (periodSelect.value !== 'custom') {
            submitFilters();
        }
    });

    formationSelect.addEventListener('change', function() {
        syncGroupOptions();
        submitFilters();
    });

    groupeSelect.addEventListener('change', submitFilters);

    [startDateInput, endDateInput].forEach((input) => {
        input.addEventListener('change', function() {
            if (periodSelect.value === 'custom' && startDateInput.value && endDateInput.value) {
                submitFilters();
            }
        });
    });

    syncCustomFields();
    syncGroupOptions();
});
</script>
@endsection
