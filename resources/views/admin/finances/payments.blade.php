@extends('layouts.admin')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i> Enregistrer un Paiement</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.finances.payments.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-graduation-cap text-muted me-1"></i> Formation</label>
                        <select id="formation_select" class="form-select" required>
                            <option value="">Sélectionner une formation...</option>
                            @foreach($inscriptions->pluck('formation')->unique('id')->sortBy('nom') as $form)
                                <option value="{{ $form->id }}">{{ $form->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-user text-muted me-1"></i> Apprenant</label>
                        <select id="inscription_select" name="inscription_id" class="form-select" required disabled>
                            <option value="">Sélectionner d'abord la formation...</option>
                        </select>
                    </div>

                    <!-- Résumé Financier -->
                    <div id="financial_summary" class="card border-0 bg-light p-3 mb-3 d-none shadow-none" style="border: 1px dashed var(--card-border) !important; background-color: rgba(64, 96, 160, 0.05) !important;">
                        <h6 class="fw-bold mb-3 text-dark small d-flex align-items-center">
                            <i class="fas fa-wallet text-primary me-2"></i> Résumé Financier
                        </h6>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Total Formation :</span>
                            <span id="summary_total" class="fw-bold text-dark">0 FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Montant Déjà Payé :</span>
                            <span id="summary_paye" class="fw-bold text-success">0 FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Statut Inscription :</span>
                            <span id="summary_status" class="badge">---</span>
                        </div>
                        <hr class="my-2 bg-secondary opacity-25">
                        <div class="d-flex justify-content-between small align-items-center">
                            <span class="text-muted fw-bold">Reste à Payer :</span>
                            <span id="summary_reste" class="fw-bold fs-6 text-danger">0 FCFA</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-bold mb-0"><i class="fas fa-money-bill-wave text-muted me-1"></i> Montant à payer (FCFA)</label>
                            <button type="button" id="pay_all_btn" class="btn btn-xs py-0 px-2 btn-outline-secondary d-none" style="font-size: 0.75rem;">
                                <i class="fas fa-coins me-1"></i> Tout payer
                            </button>
                        </div>
                        <input type="number" id="montant_input" name="montant" class="form-control mt-1" placeholder="0" min="1" required disabled>
                        <div id="montant_warning" class="text-danger small mt-2 d-none">
                            <i class="fas fa-exclamation-triangle me-1"></i> Le montant dépasse le reste à payer.
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date_paiement" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Mode</label>
                            <select name="mode_paiement" class="form-select" required>
                                <option value="espèces">Espèces</option>
                                <option value="wave">Wave</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="virement">Virement</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Référence (Optionnel)</label>
                        <input type="text" name="reference" class="form-control" placeholder="N° transaction, chèque...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Commentaire..."></textarea>
                    </div>
                    <button type="submit" class="btn btn text-white w-100 fw-bold"style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-1"></i> Valider le paiement
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-list text-primary me-2"></i> Historique des Paiements</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-4">Reçu / Date</th>
                            <th>Apprenant</th>
                            <th>Mode</th>
                            <th class="text-end">Montant</th>
                            <th class="text-center px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $p)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ $p->recu_numero }}</div>
                                    <small class="text-muted">{{ $p->date_paiement->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $p->inscription->apprenant->nom_complet }}</div>
                                    <small class="text-muted">{{ $p->inscription->formation->nom }}</small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border">{{ ucfirst($p->mode_paiement) }}</span>
                                    @if($p->reference)
                                        <div class="text-muted small" style="font-size: 0.7rem;">Ref: {{ $p->reference }}</div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($p->montant, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-center px-4">
                                    <a href="{{ route('admin.finances.payments.receipt', $p) }}" target="_blank" class="btn btn-sm btn-light border" title="Imprimer le reçu">
                                        <i class="fas fa-print text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">Aucun paiement enregistré</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($paiements->hasPages())
                <div class="card-footer bg-white">
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Les données des inscriptions passées depuis le serveur
    const inscriptions = @json($inscriptions);

    const formationSelect = document.getElementById('formation_select');
    const inscriptionSelect = document.getElementById('inscription_select');
    const financialSummary = document.getElementById('financial_summary');
    const summaryTotal = document.getElementById('summary_total');
    const summaryPaye = document.getElementById('summary_paye');
    const summaryStatus = document.getElementById('summary_status');
    const summaryReste = document.getElementById('summary_reste');
    const montantInput = document.getElementById('montant_input');
    const payAllBtn = document.getElementById('pay_all_btn');
    const montantWarning = document.getElementById('montant_warning');
    const submitBtn = document.querySelector('form button[type="submit"]');

    let selectedInscription = null;

    // Formater en monnaie FCFA
    function formatFCFA(value) {
        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
    }

    // Changement de formation
    formationSelect.addEventListener('change', function() {
        const formationId = this.value;
        
        // Vider le select des inscriptions/apprenants
        inscriptionSelect.innerHTML = '<option value="">Sélectionner un apprenant...</option>';
        financialSummary.classList.add('d-none');
        montantInput.disabled = true;
        montantInput.value = '';
        payAllBtn.classList.add('d-none');
        montantWarning.classList.add('d-none');
        submitBtn.disabled = false;
        selectedInscription = null;

        if (!formationId) {
            inscriptionSelect.disabled = true;
            return;
        }

        // Filtrer les inscriptions pour la formation sélectionnée
        const filtered = inscriptions.filter(ins => ins.formation_id == formationId);

        if (filtered.length === 0) {
            inscriptionSelect.innerHTML = '<option value="">Aucun apprenant inscrit à cette formation</option>';
            inscriptionSelect.disabled = true;
            return;
        }

        filtered.forEach(ins => {
            const text = `${ins.apprenant.prenom} ${ins.apprenant.nom} (Matricule: ${ins.apprenant.matricule})`;
            const option = document.createElement('option');
            option.value = ins.id;
            option.textContent = text;
            inscriptionSelect.appendChild(option);
        });

        inscriptionSelect.disabled = false;
    });

    // Changement d'apprenant (inscription)
    inscriptionSelect.addEventListener('change', function() {
        const insId = this.value;
        montantWarning.classList.add('d-none');
        submitBtn.disabled = false;

        if (!insId) {
            financialSummary.classList.add('d-none');
            montantInput.disabled = true;
            montantInput.value = '';
            payAllBtn.classList.add('d-none');
            selectedInscription = null;
            return;
        }

        // Trouver l'inscription sélectionnée
        selectedInscription = inscriptions.find(ins => ins.id == insId);

        if (selectedInscription) {
            const total = parseFloat(selectedInscription.montant_total);
            const paye = parseFloat(selectedInscription.montant_paye);
            const reste = total - paye;

            // Mettre à jour le résumé financier
            summaryTotal.textContent = formatFCFA(total);
            summaryPaye.textContent = formatFCFA(paye);
            summaryReste.textContent = formatFCFA(reste);

            // Gérer le badge de statut
            summaryStatus.className = 'badge';
            let statusLabel = '';
            if (selectedInscription.statut === 'validee') {
                summaryStatus.classList.add('bg-success');
                statusLabel = 'Validée';
            } else if (selectedInscription.statut === 'en_attente') {
                summaryStatus.classList.add('bg-warning', 'text-dark');
                statusLabel = 'En attente';
            } else if (selectedInscription.statut === 'annulee') {
                summaryStatus.classList.add('bg-danger');
                statusLabel = 'Annulée';
            } else if (selectedInscription.statut === 'terminee') {
                summaryStatus.classList.add('bg-info');
                statusLabel = 'Terminée';
            } else {
                summaryStatus.classList.add('bg-secondary');
                statusLabel = selectedInscription.statut;
            }
            summaryStatus.textContent = statusLabel;

            // Afficher le résumé financier
            financialSummary.classList.remove('d-none');

            // Gérer les contraintes sur le montant de paiement
            if (reste <= 0) {
                montantInput.disabled = true;
                montantInput.value = '0';
                payAllBtn.classList.add('d-none');
                
                montantWarning.classList.remove('d-none');
                montantWarning.className = 'alert alert-info py-2 px-3 mt-2 mb-0 small';
                montantWarning.innerHTML = '<i class="fas fa-check-circle me-1"></i> Cette inscription est déjà entièrement réglée.';
                submitBtn.disabled = true;
            } else {
                montantInput.disabled = false;
                montantInput.max = reste;
                montantInput.min = 1;
                montantInput.value = '';
                
                payAllBtn.classList.remove('d-none');
                payAllBtn.title = `Régler le reste de ${formatFCFA(reste)}`;
            }
        }
    });

    // Clic sur "Tout payer"
    payAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (selectedInscription) {
            const reste = parseFloat(selectedInscription.montant_total) - parseFloat(selectedInscription.montant_paye);
            montantInput.value = reste;
            // Déclencher l'événement input pour la validation
            montantInput.dispatchEvent(new Event('input'));
        }
    });

    // Validation dynamique du montant saisi
    montantInput.addEventListener('input', function() {
        if (!selectedInscription) return;

        const val = parseFloat(this.value) || 0;
        const reste = parseFloat(selectedInscription.montant_total) - parseFloat(selectedInscription.montant_paye);

        if (val > reste) {
            montantWarning.classList.remove('d-none');
            montantWarning.className = 'text-danger small mt-2';
            montantWarning.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Le montant saisi (${formatFCFA(val)}) dépasse le reste à payer (${formatFCFA(reste)}).`;
            submitBtn.disabled = true;
        } else if (val <= 0) {
            montantWarning.classList.remove('d-none');
            montantWarning.className = 'text-danger small mt-2';
            montantWarning.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Le montant doit être supérieur à 0.`;
            submitBtn.disabled = true;
        } else {
            montantWarning.classList.add('d-none');
            submitBtn.disabled = false;
        }
    });
});
</script>
@endsection

