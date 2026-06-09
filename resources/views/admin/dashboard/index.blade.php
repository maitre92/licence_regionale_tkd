@extends('layouts.admin')

@section('content')
<div class="row g-4">
    @php
        $cards = [
            ['label' => 'Nombre total de cartes', 'value' => $total_cards, 'icon' => 'fa-id-card', 'tone' => '#0f5132'],
            ['label' => 'Cartes générées', 'value' => $generated_cards, 'icon' => 'fa-qrcode', 'tone' => '#1d4ed8'],
            ['label' => 'Hommes', 'value' => $men, 'icon' => 'fa-mars', 'tone' => '#2563eb'],
            ['label' => 'Femmes', 'value' => $women, 'icon' => 'fa-venus', 'tone' => '#be185d'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-stat">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background-color: {{ $card['tone'] }};">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="display-6 fw-bold mb-0">{{ $card['value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-id-card me-2 text-success"></i> Dernières cartes</h5>
                <a href="{{ route('admin.cards.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Licence</th>
                            <th>Nom complet</th>
                            <th>Sexe</th>
                            <th>Grade</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCards as $card)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $card->licence_number }}</span></td>
                                <td class="fw-semibold">{{ $card->full_name }}</td>
                                <td>{{ $card->gender === 'F' ? 'Femme' : 'Homme' }}</td>
                                <td>{{ $card->grade ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.cards.show', $card) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-id-card mb-3" style="font-size: 42px; opacity: 0.2;"></i>
                                    <div>Aucune carte enregistrée pour le moment</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Licence Régionale TKD</h5>
                <p class="text-muted mb-4">Plateforme dédiée à la création, la personnalisation et l'impression des cartes de licence régionale.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.cards.create') }}" class="btn text-white" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-plus-circle me-1"></i> Ajouter une carte
                    </a>
                    <a href="{{ route('admin.settings', ['tab' => 'official-info']) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sliders-h me-1"></i> Personnaliser les paramètres
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-stat {
        border-radius: 8px;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex: 0 0 auto;
    }
</style>
@endsection
