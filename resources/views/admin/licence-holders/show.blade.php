@extends('layouts.admin')

@section('title', $licenceHolder->full_name)

@section('actions')
    <div class="btn-group licence-actions">
        <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="{{ route('admin.cards.card', $licenceHolder) }}" class="btn btn-outline-success">
            <i class="fas fa-id-card"></i> Générer
        </a>
        <a href="{{ route('admin.cards.download', $licenceHolder) }}" class="btn btn-outline-primary">
            <i class="fas fa-download"></i> Télécharger
        </a>
        <a href="{{ route('admin.cards.print', $licenceHolder) }}" target="_blank" class="btn btn-outline-dark">
            <i class="fas fa-print"></i> Imprimer
        </a>
        @if(Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_licence_holder')))
            <a href="{{ route('admin.cards.edit', $licenceHolder) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
        @endif
    </div>
@endsection

@section('content')
<div class="row licence-detail-page">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                @if($licenceHolder->photo_url)
                    <img src="{{ $licenceHolder->photo_url }}" alt="Photo {{ $licenceHolder->full_name }}" class="rounded-circle img-thumbnail mb-3" style="width: 170px; height: 170px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white mb-3" style="width: 170px; height: 170px; font-size: 42px; font-weight: bold;">
                        {{ mb_strtoupper(mb_substr($licenceHolder->first_name, 0, 1) . mb_substr($licenceHolder->last_name, 0, 1)) }}
                    </div>
                @endif
                <h4 class="mb-1">{{ $licenceHolder->full_name }}</h4>
                <div class="text-muted mb-3">{{ $licenceHolder->licence_number }}</div>
                <span class="badge bg-{{ $licenceHolder->status_color }}">{{ $licenceHolder->status_label }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Informations du disciple</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Numéro de licence</div>
                        <div class="fw-semibold">{{ $licenceHolder->licence_number ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Nom complet</div>
                        <div class="fw-semibold">{{ $licenceHolder->full_name ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Sexe</div>
                        <div class="fw-semibold">{{ $licenceHolder->gender === 'F' ? 'Féminin' : 'Masculin' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Date de naissance</div>
                        <div class="fw-semibold">{{ optional($licenceHolder->birth_date)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Lieu de naissance</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_place ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Téléphone</div>
                        <div class="fw-semibold">{{ $licenceHolder->phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Date de délivrance</div>
                        <div class="fw-semibold">{{ optional($licenceHolder->issued_at)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Grade</div>
                        <div class="fw-semibold">{{ $licenceHolder->grade ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Section</div>
                        <div class="fw-semibold">{{ $licenceHolder->club ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Salle</div>
                        <div class="fw-semibold">{{ $licenceHolder->salle ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Domicile</div>
                        <div class="fw-semibold">{{ $licenceHolder->domicile ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">N° acte</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_act_number ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">NINA</div>
                        <div class="fw-semibold">{{ $licenceHolder->nina ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Statut</div>
                        <div class="fw-semibold"><span class="badge bg-{{ $licenceHolder->status_color }}">{{ $licenceHolder->status_label }}</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Créé par</div>
                        <div class="fw-semibold">{{ $licenceHolder->creator->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Date d'enregistrement</div>
                        <div class="fw-semibold">{{ optional($licenceHolder->created_at)->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Dernière modification</div>
                        <div class="fw-semibold">{{ optional($licenceHolder->updated_at)->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i> Informations des parents</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Prénom(s) du père</div>
                        <div class="fw-semibold">{{ $licenceHolder->father_first_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Nom du père</div>
                        <div class="fw-semibold">{{ $licenceHolder->father_last_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Profession du père</div>
                        <div class="fw-semibold">{{ $licenceHolder->father_profession ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Domicile du père</div>
                        <div class="fw-semibold">{{ $licenceHolder->father_domicile ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Prénom(s) de la mère</div>
                        <div class="fw-semibold">{{ $licenceHolder->mother_first_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Nom de la mère</div>
                        <div class="fw-semibold">{{ $licenceHolder->mother_last_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Profession de la mère</div>
                        <div class="fw-semibold">{{ $licenceHolder->mother_profession ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Domicile de la mère</div>
                        <div class="fw-semibold">{{ $licenceHolder->mother_domicile ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i> Acte de naissance</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">N° acte</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_act_number ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">NINA</div>
                        <div class="fw-semibold">{{ $licenceHolder->nina ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Région</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_act_region ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Cercle</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_act_cercle ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Arrondissement</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_act_arrondissement ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Commune</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_act_commune ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Centre</div>
                        <div class="fw-semibold">{{ $licenceHolder->birth_act_center ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Officier d'état civil</div>
                        <div class="fw-semibold">{{ $licenceHolder->civil_officer_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Qualité de l'officier</div>
                        <div class="fw-semibold">{{ $licenceHolder->civil_officer_quality ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Date d'établissement de l'acte</div>
                        <div class="fw-semibold">{{ optional($licenceHolder->birth_act_established_at)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Date de certification</div>
                        <div class="fw-semibold">{{ optional($licenceHolder->birth_act_certified_at)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Acte importé</div>
                        @if($licenceHolder->birth_certificate_url)
                            <a href="{{ $licenceHolder->birth_certificate_url }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-1">
                                <i class="fas fa-file-image me-1"></i> Voir l'acte de naissance
                            </a>
                        @else
                            <div class="fw-semibold">-</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0"><i class="fas fa-id-card me-2"></i> Aperçu de la carte</h5>
    </div>
    <div class="card-body license-preview-panel">
        @include('admin.licence-holders._card_preview')
    </div>
</div>

<style>
    .licence-detail-page .fw-semibold,
    .licence-detail-page .card-title,
    .licence-detail-page h4,
    .licence-detail-page .text-muted {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .license-preview-panel {
        display: flex;
        justify-content: center;
        overflow: hidden;
    }

    .licence-actions {
        flex-wrap: wrap;
        gap: 4px;
    }

    .licence-actions > .btn {
        border-radius: 6px !important;
    }

    @media (max-width: 575.98px) {
        .licence-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .licence-actions > .btn {
            width: 100%;
            min-width: 0;
            white-space: normal;
        }
    }
</style>
@endsection
