@extends('layouts.admin')

@section('title', 'Rémunérations formateurs')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-0" style="background-color: var(--navbar-bg); border-radius: 10px 10px 0 0;">
                <h6 class="mb-0 fw-bold text-white"><i class="fas fa-hand-holding-usd text-warning me-2"></i> Rémunérer Formateur</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.finances.trainer_payments.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-users text-muted me-1"></i> Groupe</label>
                        <select id="groupe_select" name="groupe_formation_id" class="form-select" required>
                            <option value="">Sélectionner un groupe...</option>
                            @foreach($groupesFormation as $groupe)
                                <option value="{{ $groupe->id }}">{{ $groupe->nom }} - {{ $groupe->formation->nom ?? 'Formation' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-chalkboard-teacher text-muted me-1"></i> Formateur</label>
                        <select id="trainer_select" name="user_id" class="form-select" required disabled>
                            <option value="">Sélectionner d'abord le groupe...</option>
                        </select>
                    </div>

                    <!-- Résumé Financier de la Commission -->
                    <div id="commission_summary" class="card border-0 bg-light p-3 mb-3 d-none shadow-none" style="border: 1px dashed var(--card-border) !important; background-color: rgba(253, 126, 20, 0.05) !important;">
                        <h6 class="fw-bold mb-3 text-dark small d-flex align-items-center">
                            <i class="fas fa-percentage text-warning me-2"></i> Détails de la Commission
                        </h6>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Commission accordée :</span>
                            <span id="summary_percentage" class="fw-bold text-dark">0%</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Total Encaissé (Élèves) :</span>
                            <span id="summary_total_collecte" class="fw-bold text-dark">0 FCFA</span>
                        </div>
                        <hr class="my-2 bg-secondary opacity-25">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Commission Acquise :</span>
                            <span id="summary_commission_acquise" class="fw-bold text-success">0 FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Déjà Payé au Formateur :</span>
                            <span id="summary_deja_paye" class="fw-bold text-danger">0 FCFA</span>
                        </div>
                        <hr class="my-2 bg-secondary opacity-25">
                        <div class="d-flex justify-content-between small align-items-center">
                            <span class="text-muted fw-bold">Solde Restant à Payer :</span>
                            <span id="summary_reste" class="fw-bold fs-6 text-warning">0 FCFA</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-bold mb-0"><i class="fas fa-money-bill-wave text-muted me-1"></i> Montant à verser (FCFA)</label>
                            <button type="button" id="pay_all_btn" class="btn btn-xs py-0 px-2 btn-outline-secondary d-none" style="font-size: 0.75rem;">
                                <i class="fas fa-coins me-1"></i> Tout verser
                            </button>
                        </div>
                        <input type="number" id="montant_input" name="montant" class="form-control mt-1" placeholder="0" min="1" required disabled>
                        <div id="montant_warning" class="text-danger small mt-2 d-none">
                            <i class="fas fa-exclamation-triangle me-1"></i> Le montant dépasse le solde restant.
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date de versement</label>
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

                    <button type="submit" class="btn btn text-white w-100 fw-bold" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-1"></i> Enregistrer le versement
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar text-danger me-2"></i> Historique des Versements Formateurs</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-4">Date</th>
                            <th>Formateur / Groupe</th>
                            <th>Mode / Réf</th>
                            <th class="text-end">Montant Versé</th>
                            <th class="text-center px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td class="px-4 small text-muted">{{ $p->date_depense->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $p->trainer->name ?? $p->beneficiaire }}</div>
                                    <span class="badge bg-light text-dark border small" style="font-size: 0.65rem;">
                                        {{ $p->groupeFormation->nom ?? $p->formation->nom ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst($p->mode_paiement ?? 'espèces') }}</span>
                                    @if($p->reference)
                                        <div class="text-muted small" style="font-size: 0.7rem;">Ref: {{ $p->reference }}</div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-danger">{{ number_format($p->montant, 0, ',', ' ') }} FCFA</td>
                                <td class="text-center px-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.finances.trainer_payments.receipt', $p) }}" target="_blank" class="btn btn-sm btn-light border" title="Imprimer le reçu">
                                            <i class="fas fa-print text-primary"></i>
                                        </a>
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_trainer_payment'))
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-payment" 
                                                    data-id="{{ $p->id }}"
                                                    data-trainer-name="{{ $p->trainer->name ?? $p->beneficiaire }}"
                                                    data-formation-name="{{ $p->formation->nom ?? 'N/A' }}"
                                                    data-montant="{{ $p->montant }}"
                                                    data-date_paiement="{{ $p->date_depense->format('Y-m-d') }}"
                                                    data-mode_paiement="{{ $p->mode_paiement }}"
                                                    data-reference="{{ $p->reference }}"
                                                    data-notes="{{ $p->notes }}"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_trainer_payment'))
                                            <form action="{{ route('admin.finances.trainer_payments.destroy', $p) }}" method="POST" class="delete-payment-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-payment" data-trainer="{{ $p->trainer->name ?? $p->beneficiaire }}" title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Aucun versement enregistré</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="card-footer bg-white">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Charger les données pré-calculées depuis Laravel
    const formationsData = @json($formationsData);

    const formationSelect = document.getElementById('groupe_select');
    const trainerSelect = document.getElementById('trainer_select');
    const commissionSummary = document.getElementById('commission_summary');
    
    const summaryPercentage = document.getElementById('summary_percentage');
    const summaryTotalCollecte = document.getElementById('summary_total_collecte');
    const summaryCommissionAcquise = document.getElementById('summary_commission_acquise');
    const summaryDejaPaye = document.getElementById('summary_deja_paye');
    const summaryReste = document.getElementById('summary_reste');
    
    const montantInput = document.getElementById('montant_input');
    const payAllBtn = document.getElementById('pay_all_btn');
    const montantWarning = document.getElementById('montant_warning');
    const submitBtn = document.querySelector('form button[type="submit"]');

    let selectedFormation = null;
    let selectedTrainer = null;

    // Formater en monnaie FCFA
    function formatFCFA(value) {
        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
    }

    // Changement de groupe
    formationSelect.addEventListener('change', function() {
        const formationId = this.value;
        
        // Réinitialiser les sélecteurs
        trainerSelect.innerHTML = '<option value="">Sélectionner un formateur...</option>';
        commissionSummary.classList.add('d-none');
        montantInput.disabled = true;
        montantInput.value = '';
        payAllBtn.classList.add('d-none');
        montantWarning.classList.add('d-none');
        submitBtn.disabled = false;
        
        selectedFormation = null;
        selectedTrainer = null;

        if (!formationId) {
            trainerSelect.disabled = true;
            return;
        }

        // Trouver les détails du groupe
        selectedFormation = formationsData.find(f => f.id == formationId);

        if (!selectedFormation || selectedFormation.formateurs.length === 0) {
            trainerSelect.innerHTML = '<option value="">Aucun formateur pour ce groupe</option>';
            trainerSelect.disabled = true;
            return;
        }

        // Remplir le dropdown des formateurs
        selectedFormation.formateurs.forEach(trainer => {
            const option = document.createElement('option');
            option.value = trainer.id;
            const commissionLabel = trainer.commission_type === 'montant'
                ? formatFCFA(trainer.fixed_amount)
                : `${trainer.percentage}%`;
            option.textContent = `${trainer.name} (${commissionLabel})`;
            trainerSelect.appendChild(option);
        });

        trainerSelect.disabled = false;
    });

    // Changement de formateur
    trainerSelect.addEventListener('change', function() {
        const trainerId = this.value;
        montantWarning.classList.add('d-none');
        submitBtn.disabled = false;

        if (!trainerId || !selectedFormation) {
            commissionSummary.classList.add('d-none');
            montantInput.disabled = true;
            montantInput.value = '';
            payAllBtn.classList.add('d-none');
            selectedTrainer = null;
            return;
        }

        // Trouver le formateur sélectionné
        selectedTrainer = selectedFormation.formateurs.find(t => t.id == trainerId);

        if (selectedTrainer) {
            const commissionLabel = selectedTrainer.commission_type === 'montant'
                ? formatFCFA(selectedTrainer.fixed_amount)
                : `${selectedTrainer.percentage}%`;
            const commissionAcquise = selectedTrainer.commission_acquise;
            const dejaPaye = selectedTrainer.deja_paye;
            const reste = selectedTrainer.reste_a_payer;

            // Remplir le résumé
            summaryPercentage.textContent = commissionLabel;
            summaryTotalCollecte.textContent = formatFCFA(selectedFormation.total_collecte);
            summaryCommissionAcquise.textContent = formatFCFA(commissionAcquise);
            summaryDejaPaye.textContent = formatFCFA(dejaPaye);
            summaryReste.textContent = formatFCFA(reste);

            commissionSummary.classList.remove('d-none');

            // Vérifier le solde de paiement
            if (reste <= 0) {
                montantInput.disabled = true;
                montantInput.value = '0';
                payAllBtn.classList.add('d-none');
                
                montantWarning.classList.remove('d-none');
                montantWarning.className = 'alert alert-info py-2 px-3 mt-2 mb-0 small';
                montantWarning.innerHTML = '<i class="fas fa-check-circle me-1"></i> Ce formateur est déjà entièrement réglé pour cette session.';
                submitBtn.disabled = true;
            } else {
                montantInput.disabled = false;
                montantInput.max = reste;
                montantInput.min = 1;
                montantInput.value = '';
                
                payAllBtn.classList.remove('d-none');
            }
        }
    });

    // Clic sur "Tout payer"
    payAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (selectedTrainer) {
            montantInput.value = selectedTrainer.reste_a_payer;
            montantInput.dispatchEvent(new Event('input'));
        }
    });

    // Validation dynamique du montant
    montantInput.addEventListener('input', function() {
        if (!selectedTrainer) return;

        const val = parseFloat(this.value) || 0;
        const reste = selectedTrainer.reste_a_payer;

        if (val > reste) {
            montantWarning.classList.remove('d-none');
            montantWarning.className = 'text-danger small mt-2';
            montantWarning.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Le montant dépasse le solde restant dû (${formatFCFA(reste)}).`;
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

    // Modal de modification de versement formateur (triggers)
    const editModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
    const editForm = document.getElementById('editPaymentForm');
    
    document.querySelectorAll('.btn-edit-payment').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('edit_trainer_display').value = `${this.dataset.trainerName} - ${this.dataset.formationName}`;
            document.getElementById('edit_montant').value = this.dataset.montant;
            document.getElementById('edit_date_paiement').value = this.dataset.date_paiement;
            document.getElementById('edit_mode_paiement').value = this.dataset.mode_paiement;
            document.getElementById('edit_reference').value = this.dataset.reference || '';
            document.getElementById('edit_notes').value = this.dataset.notes || '';
            
            editForm.action = `/admin/finances/formateurs/${id}`;
            editModal.show();
        });
    });

    document.querySelectorAll('.btn-delete-payment').forEach(button => {
        button.addEventListener('click', function() {
            const trainer = this.dataset.trainer;
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Supprimer le versement',
                text: `Voulez-vous vraiment supprimer le versement de commission pour le formateur "${trainer}" ?`,
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
                    form.submit();
                }
            });
        });
    });
});
</script>

<!-- Modal de modification de versement formateur -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editPaymentModalLabel"><i class="fas fa-edit text-warning"></i> Modifier le Versement Formateur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="editPaymentForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Formation & Formateur</label>
                        <input type="text" id="edit_trainer_display" class="form-control bg-light" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Montant (FCFA)</label>
                        <input type="number" id="edit_montant" name="montant" class="form-control bg-light" readonly required min="1">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date de versement</label>
                            <input type="date" id="edit_date_paiement" name="date_paiement" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Mode</label>
                            <select id="edit_mode_paiement" name="mode_paiement" class="form-select" required>
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
                        <input type="text" id="edit_reference" name="reference" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea id="edit_notes" name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
