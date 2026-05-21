@extends('layouts.admin')

@section('title', 'Détails Apprenant')

@section('actions')
    <a href="{{ route('admin.apprenants.index') }}" class="btn btn-outline-secondary me-2">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
    @can('edit_learner')
        <a href="{{ route('admin.apprenants.edit', $apprenant) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier
        </a>
    @endcan
@endsection

@section('content')
<div class="row">
    <!-- Profil Card (Left Column) -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="position-relative d-inline-block mb-4">
                    @if($apprenant->photo_url)
                        <img src="{{ $apprenant->photo_url }}" class="rounded-circle img-thumbnail shadow-sm" style="width: 150px; height: 150px; object-fit: cover;" alt="Photo {{ $apprenant->nom }}">
                    @else
                        <div class="rounded-circle shadow-sm bg-primary d-flex align-items-center justify-content-center text-white mx-auto" style="width: 150px; height: 150px; font-size: 50px; font-weight: bold;">
                            {{ $apprenant->initiales }}
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 p-2 border border-white rounded-circle bg-{{ $apprenant->statut->color() ?? 'secondary' }}" title="Statut: {{ $apprenant->statut->label() ?? $apprenant->statut }}">
                        <span class="visually-hidden">Statut: {{ $apprenant->statut->label() ?? $apprenant->statut }}</span>
                    </span>
                </div>
                
                <h4 class="mb-1 fw-bold">{{ $apprenant->nom_complet }}</h4>
                <div class="text-muted mb-3 d-flex align-items-center justify-content-center gap-2">
                    <span class="badge bg-light text-dark border"><i class="fas fa-id-card text-muted me-1"></i> {{ $apprenant->matricule }}</span>
                </div>

                <div class="d-flex justify-content-center gap-2 mb-4">
                    @if($apprenant->email)
                        <a href="mailto:{{ $apprenant->email }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Envoyer un email"><i class="fas fa-envelope"></i></a>
                    @endif
                    @if($apprenant->telephone)
                        <a href="tel:{{ $apprenant->telephone }}" class="btn btn-sm btn-outline-success rounded-circle" title="Appeler"><i class="fas fa-phone"></i></a>
                    @endif
                </div>

                <hr class="text-muted opacity-25 my-4">

                <ul class="list-unstyled text-start mb-0">
                    <li class="d-flex mb-3">
                        <div class="me-3 mt-1"><i class="fas fa-envelope text-primary fa-fw"></i></div>
                        <div>
                            <span class="d-block text-muted small">Email</span>
                            <span class="fw-medium">{{ $apprenant->email ?? 'Non renseigné' }}</span>
                        </div>
                    </li>
                    <li class="d-flex mb-3">
                        <div class="me-3 mt-1"><i class="fas fa-phone text-primary fa-fw"></i></div>
                        <div>
                            <span class="d-block text-muted small">Téléphone</span>
                            <span class="fw-medium">{{ $apprenant->telephone ?? 'Non renseigné' }}</span>
                        </div>
                    </li>
                    <li class="d-flex mb-3">
                        <div class="me-3 mt-1"><i class="fas fa-map-marker-alt text-primary fa-fw"></i></div>
                        <div>
                            <span class="d-block text-muted small">Adresse</span>
                            <span class="fw-medium">{{ $apprenant->adresse ?? 'Non renseignée' }}</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="card-footer bg-light border-0 py-3 text-center">
                <small class="text-muted">Inscrit le : {{ $apprenant->date_inscription?->format('d/m/Y') }}</small><br>
                @if($apprenant->createdBy)
                    <small class="text-muted">Enregistré par : {{ $apprenant->createdBy->name }}</small>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Tabs (Right Column) -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white pt-3 pb-0 border-bottom-0">
                <ul class="nav nav-tabs border-bottom-0" id="apprenantTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-medium text-dark" id="infos-tab" data-bs-toggle="tab" data-bs-target="#infos" type="button" role="tab" aria-controls="infos" aria-selected="true">
                            <i class="fas fa-info-circle me-1"></i> Informations générales
                        </button>
                    </li>
                    <!-- Future Onglets -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-muted" id="formations-tab" data-bs-toggle="tab" data-bs-target="#formations" type="button" role="tab" aria-controls="formations" aria-selected="false">
                            <i class="fas fa-book me-1"></i> Formations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-muted" id="paiements-tab" data-bs-toggle="tab" data-bs-target="#paiements" type="button" role="tab" aria-controls="paiements" aria-selected="false">
                            <i class="fas fa-credit-card me-1"></i> Paiements
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body border-top">
                <div class="tab-content" id="apprenantTabsContent">
                    <!-- Tab: Informations -->
                    <div class="tab-pane fade show active" id="infos" role="tabpanel" aria-labelledby="infos-tab">
                        
                        <h6 class="text-primary text-uppercase fw-bold mb-3 mt-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">Détails personnels</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Nom de famille</p>
                                <p class="fw-medium">{{ $apprenant->nom }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Prénom(s)</p>
                                <p class="fw-medium">{{ $apprenant->prenom }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Sexe</p>
                                <p class="fw-medium">{{ $apprenant->sexe === 'M' ? 'Masculin' : 'Féminin' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Date de naissance</p>
                                <p class="fw-medium">
                                    {{ $apprenant->date_naissance ? $apprenant->date_naissance->format('d/m/Y') : 'Non renseignée' }}
                                    @if($apprenant->date_naissance)
                                        <span class="text-muted small">({{ \Carbon\Carbon::parse($apprenant->date_naissance)->age }} ans)</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        <h6 class="text-primary text-uppercase fw-bold mb-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Informations académiques</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Niveau d'étude</p>
                                <p class="fw-medium">
                                    <span class="badge bg-light text-dark border">{{ $apprenant->niveau_etude->label() ?? $apprenant->niveau_etude }}</span>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Profession</p>
                                <p class="fw-medium">{{ $apprenant->profession ?? 'Non renseignée' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Statut actuel</p>
                                <p class="fw-medium">
                                    <span class="badge bg-{{ $apprenant->statut->color() ?? 'secondary' }}">
                                        {{ $apprenant->statut->label() ?? $apprenant->statut }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Date d'inscription</p>
                                <p class="fw-medium">{{ $apprenant->date_inscription ? $apprenant->date_inscription->format('d/m/Y') : 'Non renseignée' }}</p>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        <h6 class="text-primary text-uppercase fw-bold mb-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Contact d'urgence (Parent / Tuteur)</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Nom du contact</p>
                                <p class="fw-medium">{{ $apprenant->contact_parent ?? 'Non renseigné' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted small">Téléphone d'urgence</p>
                                <p class="fw-medium">
                                    @if($apprenant->telephone_parent)
                                        <a href="tel:{{ $apprenant->telephone_parent }}" class="text-decoration-none">
                                            <i class="fas fa-phone-alt small text-muted me-1"></i> {{ $apprenant->telephone_parent }}
                                        </a>
                                    @else
                                        Non renseigné
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($apprenant->observations)
                            <hr class="text-muted opacity-25 my-4">
                            <h6 class="text-primary text-uppercase fw-bold mb-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Observations</h6>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($apprenant->observations)) !!}
                            </div>
                        @endif

                    </div>

                    <!-- Tab: Formations -->
                    <div class="tab-pane fade" id="formations" role="tabpanel" aria-labelledby="formations-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Formation</th>
                                        <th>Date Inscription</th>
                                        <th>Statut</th>
                                        <th>Montant</th>
                                        <th>Reste à payer</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($apprenant->formations as $formation)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $formation->nom }}</div>
                                            <small class="text-muted">{{ $formation->code }}</small>
                                        </td>
                                        <td>{{ $formation->pivot->date_inscription ? \Carbon\Carbon::parse($formation->pivot->date_inscription)->format('d/m/Y') : '---' }}</td>
                                        <td>
                                            @php
                                                $statutColors = [
                                                    'validee' => 'success',
                                                    'en_attente' => 'warning',
                                                    'annulee' => 'danger',
                                                    'terminee' => 'info'
                                                ];
                                                $color = $statutColors[$formation->pivot->statut] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($formation->pivot->statut) }}</span>
                                        </td>
                                        <td>{{ number_format($formation->pivot->montant_total, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-danger fw-bold">
                                            {{ number_format($formation->pivot->montant_total - $formation->pivot->montant_paye, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.formations.show', $formation) }}" class="btn btn-sm btn-outline-info rounded-circle"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Cet apprenant n'est inscrit à aucune formation.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.formations.index') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Nouvelle inscription
                            </a>
                        </div>
                    </div>

                    <!-- Tab: Paiements -->
                    <div class="tab-pane fade" id="paiements" role="tabpanel" aria-labelledby="paiements-tab">
                        @php
                            $allPaiements = $apprenant->inscriptions->flatMap(function($ins) {
                                return $ins->paiements;
                            })->sortByDesc('date_paiement');
                        @endphp

                        @if($allPaiements->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-file-invoice-dollar text-muted mb-3" style="font-size: 3rem; opacity: 0.2;"></i>
                                <h5>Aucun paiement enregistré</h5>
                                <p class="text-muted">Les règlements effectués par cet apprenant s'afficheront ici.</p>
                                <a href="{{ route('admin.finances.payments') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus me-1"></i> Enregistrer un paiement
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light text-uppercase" style="font-size: 0.75rem;">
                                        <tr>
                                            <th>Reçu / Date</th>
                                            <th>Formation</th>
                                            <th>Mode</th>
                                            <th>Enregistré par</th>
                                            <th class="text-end">Montant</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allPaiements as $p)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $p->recu_numero }}</div>
                                                    <small class="text-muted">{{ $p->date_paiement->format('d/m/Y') }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-medium">{{ $p->inscription->formation->nom }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill bg-light text-dark border">{{ ucfirst($p->mode_paiement) }}</span>
                                                    @if($p->reference)
                                                        <div class="text-muted small" style="font-size: 0.7rem;">Ref: {{ $p->reference }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $p->creator->name ?? 'Système' }}</small>
                                                </td>
                                                <td class="text-end fw-bold text-success">
                                                    {{ number_format($p->montant, 0, ',', ' ') }} FCFA
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.finances.payments.receipt', $p) }}" target="_blank" class="btn btn-sm btn-light border" title="Imprimer le reçu">
                                                        <i class="fas fa-print text-primary"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
