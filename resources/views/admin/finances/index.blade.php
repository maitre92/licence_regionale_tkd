@extends('layouts.admin')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm  text-white p-3" style="background-color: var(--navbar-bg);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-white bg-opacity-25 rounded-pill p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill">Total Recettes</span>
                </div>
                <h2 class="fw-bold mb-1">{{ number_format($total_revenue, 0, ',', ' ') }} FCFA</h2>
                <p class="small mb-0 text-white-50">Flux entrant total cumulé</p>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm text-white p-3" style="background-color: var(--navbar-bg);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-white bg-opacity-25 rounded-pill p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-arrow-trend-down"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill">Total Dépenses</span>
                </div>
                <h2 class="fw-bold mb-1">{{ number_format($total_expenses, 0, ',', ' ') }} FCFA</h2>
                <p class="small mb-0 text-white-50">Total des charges enregistrées</p>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm {{ $balance >= 0 ? 'background-color: var(--navbar-bg);' : 'bg-warning' }} text-white p-3"style="background-color: var(--navbar-bg);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-white bg-opacity-25 rounded-pill p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-scale-balanced"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill">Solde Net</span>
                </div>
                <h2 class="fw-bold mb-1">{{ number_format($balance, 0, ',', ' ') }} FCFA</h2>
                <p class="small mb-0 text-white-50">Balance financière actuelle</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-chart-area text-primary me-2"></i> Évolution mensuelle (Recettes vs Dépenses)</h6>
            </div>
            <div class="card-body">
                <canvas id="financeChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-bolt text-warning me-2"></i> Actions Rapides</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.finances.payments') }}" class="btn btn py-3 fw-bold text-white" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-plus-circle me-2"></i> Nouveau Paiement
                    </a>
                    <a href="{{ route('admin.finances.expenses') }}" class="btn btn-outline-danger py-3 fw-bold">
                        <i class="fas fa-minus-circle me-2"></i> Enregistrer Dépense
                    </a>
                    <a href="{{ route('admin.finances.trainer_payments') }}" class="btn btn-outline-warning py-3 fw-bold">
                        <i class="fas fa-hand-holding-usd me-2"></i> Rémunérer Formateur
                    </a>
                    <hr class="my-2">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small fw-bold text-muted mb-2">RÉSUMÉ DU MOIS</div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Recettes</span>
                            <span class="small fw-bold text-success">+ {{ number_format($revenue_by_month->last(), 0, ',', ' ') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Dépenses</span>
                            <span class="small fw-bold text-danger">- {{ number_format($expenses_by_month->last(), 0, ',', ' ') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-history text-muted me-2"></i> Derniers Paiements</h6>
                <a href="{{ route('admin.finances.payments') }}" class="btn btn-sm btn-link p-0 text-decoration-none">Tout voir</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small">Reçu</th>
                            <th class="border-0 small">Apprenant</th>
                            <th class="border-0 small text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_paiements as $p)
                            <tr>
                                <td class="small fw-bold">{{ $p->recu_numero }}</td>
                                <td>
                                    <div class="small fw-bold">{{ $p->inscription->apprenant->nom_complet }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $p->inscription->formation->nom }}</div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-success small">{{ number_format($p->montant, 0, ',', ' ') }}</div>
                                    <a href="{{ route('admin.finances.payments.receipt', $p) }}" target="_blank" class="text-muted" style="font-size: 0.65rem;">
                                        <i class="fas fa-print me-1"></i>Reçu
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-receipt text-muted me-2"></i> Dernières Dépenses</h6>
                <a href="{{ route('admin.finances.expenses') }}" class="btn btn-sm btn-link p-0 text-decoration-none">Tout voir</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small">Catégorie</th>
                            <th class="border-0 small">Titre</th>
                            <th class="border-0 small text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_depenses as $d)
                            <tr>
                                <td><span class="badge bg-light text-dark border small">{{ $d->categorie }}</span></td>
                                <td class="small fw-bold">{{ $d->titre }}</td>
                                <td class="text-end fw-bold text-danger small">{{ number_format($d->montant, 0, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financeChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [
                {
                    label: 'Recettes',
                    data: @json($revenue_by_month),
                    backgroundColor: 'rgba(78, 84, 200, 0.8)',
                    borderRadius: 5
                },
                {
                    label: 'Dépenses',
                    data: @json($expenses_by_month),
                    backgroundColor: 'rgba(235, 51, 73, 0.8)',
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true, grid: { drawBorder: false, color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endsection
